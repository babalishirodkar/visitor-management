<?php
/**
 * Gate Operations API Endpoint
 * GateDesk - Visitor Management & Gate Pass System
 */

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json; charset=utf-8');

$input = api_input();
$action = $input['action'] ?? 'stats';
$db = get_db();

// Resolve active site ID (default to Site 1 / Pune Plant)
$currentSiteName = $_SESSION['site'] ?? $SITES[0] ?? 'Pune Plant';
$stmtSite = $db->prepare("SELECT id FROM sites WHERE site_name = ? LIMIT 1");
$stmtSite->execute([$currentSiteName]);
$siteId = $stmtSite->fetchColumn() ?: 1;

try {
    switch ($action) {
        /**
         * GET Occupancy & Gate Statistics
         */
        case 'stats':
            // 1. People currently inside
            $stmtInside = $db->prepare("SELECT COUNT(*) FROM gate_logs WHERE site_id = ? AND occupancy_status = 'Inside'");
            $stmtInside->execute([$siteId]);
            $insideCount = (int)$stmtInside->fetchColumn();

            // 2. Entered today
            $stmtInToday = $db->prepare("SELECT COUNT(*) FROM gate_logs WHERE site_id = ? AND DATE(check_in_time) = CURDATE()");
            $stmtInToday->execute([$siteId]);
            $enteredToday = (int)$stmtInToday->fetchColumn();

            // 3. Exited today
            $stmtOutToday = $db->prepare("SELECT COUNT(*) FROM gate_logs WHERE site_id = ? AND DATE(check_out_time) = CURDATE()");
            $stmtOutToday->execute([$siteId]);
            $exitedToday = (int)$stmtOutToday->fetchColumn();

            // 4. Expected appointments today
            $stmtExpected = $db->prepare("SELECT COUNT(*) FROM appointments WHERE site_id = ? AND scheduled_date = CURDATE() AND status IN ('Approved', 'Pending approval')");
            $stmtExpected->execute([$siteId]);
            $expectedToday = (int)$stmtExpected->fetchColumn();

            // 5. Overdue (Inside > 8 hours or past scheduled time)
            $stmtOverdue = $db->prepare("SELECT COUNT(*) FROM gate_logs WHERE site_id = ? AND occupancy_status = 'Inside' AND check_in_time < DATE_SUB(NOW(), INTERVAL 8 HOUR)");
            $stmtOverdue->execute([$siteId]);
            $overdueToday = (int)$stmtOverdue->fetchColumn();

            api_response([
                'inside'   => $insideCount,
                'entered'  => $enteredToday,
                'exited'   => $exitedToday,
                'expected' => $expectedToday,
                'overdue'  => $overdueToday,
                'site_id'  => $siteId,
                'site_name'=> $currentSiteName,
            ]);
            break;

        /**
         * GET Currently On Site List
         */
        case 'onsite':
            $sql = "SELECT 
                        l.id AS log_id,
                        l.check_in_time,
                        p.id AS pass_id,
                        p.pass_number,
                        p.pass_type,
                        v.name AS visitor_name,
                        v.company AS visitor_company,
                        u.name AS host_name,
                        g.gate_name
                    FROM gate_logs l
                    JOIN gate_passes p ON l.pass_id = p.id
                    LEFT JOIN visitors v ON p.visitor_id = v.id
                    LEFT JOIN users u ON p.host_user_id = u.id
                    LEFT JOIN gates g ON l.gate_id = g.id
                    WHERE l.site_id = ? AND l.occupancy_status = 'Inside'
                    ORDER BY l.check_in_time DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute([$siteId]);
            $rows = $stmt->fetchAll();

            $list = [];
            foreach ($rows as $r) {
                // Calculate duration inside
                $inTime = strtotime($r['check_in_time']);
                $diff = time() - $inTime;
                $hours = floor($diff / 3600);
                $mins = floor(($diff % 3600) / 60);

                $list[] = [
                    'pass'     => $r['pass_number'],
                    'name'     => $r['visitor_name'] ?? 'Visitor',
                    'company'  => $r['visitor_company'] ?? 'Self',
                    'type'     => $r['pass_type'] ?? 'Visitor',
                    'host'     => $r['host_name'] ?? 'Host',
                    'gate'     => $r['gate_name'] ?? 'Gate 1',
                    'in'       => date('H:i', $inTime),
                    'duration' => "{$hours} h {$mins} m",
                ];
            }
            api_response($list);
            break;

        /**
         * GET Arriving Next (Upcoming Appointments for Today)
         */
        case 'arriving':
            $sql = "SELECT 
                        a.appointment_ref,
                        a.start_time,
                        a.accompanying_count,
                        v.name AS visitor_name,
                        u.name AS host_name
                    FROM appointments a
                    JOIN visitors v ON a.visitor_id = v.id
                    JOIN users u ON a.host_user_id = u.id
                    WHERE a.site_id = ? 
                      AND a.scheduled_date = CURDATE() 
                      AND a.status = 'Approved'
                    ORDER BY a.start_time ASC
                    LIMIT 5";
            $stmt = $db->prepare($sql);
            $stmt->execute([$siteId]);
            $rows = $stmt->fetchAll();

            $list = [];
            foreach ($rows as $r) {
                $groupText = $r['accompanying_count'] > 0 ? " (+{$r['accompanying_count']})" : "";
                $list[] = [
                    'ref'     => $r['appointment_ref'],
                    'visitor' => $r['visitor_name'] . $groupText,
                    'host'    => $r['host_name'],
                    'time'    => substr($r['start_time'], 0, 5),
                ];
            }
            api_response($list);
            break;

        /**
         * GET Exceptions Today from Audit Log
         */
        case 'exceptions':
            $sql = "SELECT action, record_ref, reason, actor_name, created_at 
                    FROM audit_logs 
                    WHERE DATE(created_at) = CURDATE()
                      AND action IN ('Denied entry', 'Invalid pass', 'Emergency override', 'Timestamp corrected')
                    ORDER BY created_at DESC 
                    LIMIT 10";
            $stmt = $db->query($sql);
            $rows = $stmt->fetchAll();

            $list = [];
            foreach ($rows as $r) {
                $list[] = [
                    'action' => $r['action'],
                    'ref'    => $r['record_ref'],
                    'reason' => $r['reason'],
                    'actor'  => $r['actor_name'],
                    'time'   => date('H:i', strtotime($r['created_at'])),
                ];
            }
            api_response($list);
            break;

        /**
         * GET / POST Scan & Lookup Pass / Visitor / Appointment
         */
        case 'lookup':
            $q = trim($input['q'] ?? '');
            if ($q === '') {
                api_error('Query term is required.');
            }

            // 1. Search Watchlist first for any active match
            $stmtWatch = $db->prepare("SELECT * FROM watchlist WHERE (person_name LIKE ? OR phone LIKE ?) AND status = 'Active' LIMIT 1");
            $stmtWatch->execute(["%{$q}%", "%{$q}%"]);
            $watchMatch = $stmtWatch->fetch();

            // 2. Search Gate Passes
            $stmtPass = $db->prepare("SELECT p.*, v.name AS visitor_name, v.company AS visitor_company, u.name AS host_name
                                      FROM gate_passes p
                                      LEFT JOIN visitors v ON p.visitor_id = v.id
                                      LEFT JOIN users u ON p.host_user_id = u.id
                                      WHERE p.pass_number = ? LIMIT 1");
            $stmtPass->execute([$q]);
            $pass = $stmtPass->fetch();

            // 3. Search Appointments if pass not matched directly
            $apt = null;
            if (!$pass) {
                $stmtApt = $db->prepare("SELECT a.*, v.name AS visitor_name, v.company AS visitor_company, u.name AS host_name
                                         FROM appointments a
                                         JOIN visitors v ON a.visitor_id = v.id
                                         JOIN users u ON a.host_user_id = u.id
                                         WHERE (a.appointment_ref = ? OR v.name LIKE ? OR v.phone LIKE ?)
                                         ORDER BY a.scheduled_date DESC LIMIT 1");
                $stmtApt->execute([$q, "%{$q}%", "%{$q}%"]);
                $apt = $stmtApt->fetch();
            }

            // If neither pass nor appointment found, return not found
            if (!$pass && !$apt && !$watchMatch) {
                api_response(null, 'No record found matching query.');
            }

            // Determine status, checks and details
            $name = $pass['visitor_name'] ?? $apt['visitor_name'] ?? $watchMatch['person_name'] ?? $q;
            $company = $pass['visitor_company'] ?? $apt['visitor_company'] ?? $watchMatch['company_name'] ?? 'Self';
            $host = $pass['host_name'] ?? $apt['host_name'] ?? 'Host';
            $checks = [];
            $state = 'ready';
            $why = '';

            // Check if on watchlist
            if ($watchMatch) {
                $state = ($watchMatch['action_to_take'] === 'Deny entry') ? 'blocked' : 'watch';
                $why = "Watchlist alert: {$watchMatch['reason']} (Action: {$watchMatch['action_to_take']})";
                $checks[] = ['warn', 'Watchlist match'];
            }

            // Pass status check
            if ($pass) {
                if ($pass['status'] === 'Revoked') {
                    $state = 'blocked';
                    $why = 'This pass has been revoked.';
                    $checks[] = ['bad', 'Pass revoked'];
                } elseif ($pass['status'] === 'Expired' || strtotime($pass['valid_until']) < time()) {
                    $state = 'blocked';
                    $why = 'Pass has expired.';
                    $checks[] = ['bad', 'Pass expired'];
                } else {
                    // Check if already inside
                    $stmtLog = $db->prepare("SELECT id FROM gate_logs WHERE pass_id = ? AND occupancy_status = 'Inside' LIMIT 1");
                    $stmtLog->execute([$pass['id']]);
                    if ($stmtLog->fetch()) {
                        $state = 'inside';
                        $checks[] = ['ok', 'Identity matched'];
                        $checks[] = ['ok', 'Pass active'];
                    } else {
                        $state = ($state === 'blocked' || $state === 'watch') ? $state : 'ready';
                        $checks[] = ['ok', 'Identity matched'];
                        $checks[] = ['ok', 'Pass valid today'];
                    }
                }
            } elseif ($apt) {
                if ($apt['status'] === 'Pending approval') {
                    $state = 'blocked';
                    $why = 'Approval pending. Entry is not allowed until approved.';
                    $checks[] = ['bad', 'Approval pending'];
                } elseif ($apt['status'] === 'Approved') {
                    $state = ($state === 'blocked' || $state === 'watch') ? $state : 'ready';
                    $checks[] = ['ok', "Approved by {$host}"];
                    $checks[] = ['ok', "Scheduled for today {$apt['start_time']}"];
                } elseif ($apt['status'] === 'Checked in') {
                    $state = 'inside';
                    $checks[] = ['ok', 'Already checked in'];
                } else {
                    $state = 'blocked';
                    $why = "Appointment status is {$apt['status']}.";
                    $checks[] = ['bad', "Status: {$apt['status']}"];
                }
            }

            api_response([
                'name'    => $name,
                'co'      => $company,
                'host'    => $host,
                'state'   => $state,
                'why'     => $why,
                'checks'  => $checks,
                'pass_id' => $pass['id'] ?? null,
                'apt_id'  => $apt['id'] ?? null,
            ]);
            break;

        /**
         * POST Record Entry (Check-In)
         */
        case 'entry':
            $passId = $input['pass_id'] ?? null;
            $aptId = $input['apt_id'] ?? null;
            $guardId = $_SESSION['auth_user']['id'] ?? 1;
            $gateId = 1; // Default to Gate 1

            // If we only have an appointment, create an active gate pass on the fly
            if (!$passId && $aptId) {
                $stmtApt = $db->prepare("SELECT * FROM appointments WHERE id = ?");
                $stmtApt->execute([$aptId]);
                $aptData = $stmtApt->fetch();

                if ($aptData) {
                    $passNum = 'GP-' . date('y') . '-' . str_pad((string)rand(1000, 9999), 5, '0', STR_PAD_LEFT);
                    $stmtCreatePass = $db->prepare("INSERT INTO gate_passes 
                        (pass_number, pass_type, appointment_id, visitor_id, site_id, host_user_id, issued_at, valid_until, status)
                        VALUES (?, 'Visitor', ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 8 HOUR), 'Active')");
                    $stmtCreatePass->execute([$passNum, $aptId, $aptData['visitor_id'], $aptData['site_id'], $aptData['host_user_id']]);
                    $passId = $db->lastInsertId();

                    // Update appointment status to Checked in
                    $stmtUpApt = $db->prepare("UPDATE appointments SET status = 'Checked in' WHERE id = ?");
                    $stmtUpApt->execute([$aptId]);
                }
            }

            if (!$passId) {
                api_error('Valid pass or appointment is required to record entry.');
            }

            // Check duplicate check-in
            $stmtCheckDup = $db->prepare("SELECT id FROM gate_logs WHERE pass_id = ? AND occupancy_status = 'Inside'");
            $stmtCheckDup->execute([$passId]);
            if ($stmtCheckDup->fetch()) {
                api_error('Visitor is already recorded inside site. Duplicate check-in prevented.');
            }

            // Insert gate log
            $stmtLog = $db->prepare("INSERT INTO gate_logs 
                (pass_id, site_id, gate_id, check_in_time, check_in_guard_id, occupancy_status)
                VALUES (?, ?, ?, NOW(), ?, 'Inside')");
            $stmtLog->execute([$passId, $siteId, $gateId, $guardId]);

            // Audit Log
            $stmtAudit = $db->prepare("INSERT INTO audit_logs (actor_name, actor_role, action, record_ref, reason) VALUES (?, ?, ?, ?, ?)");
            $stmtAudit->execute([$_SESSION['auth_user']['name'] ?? 'Guard', 'Security', 'Entry recorded', (string)$passId, 'Gate entry completed']);

            api_response(['pass_id' => $passId], 'Entry recorded successfully.');
            break;

        /**
         * POST Record Exit (Check-Out)
         */
        case 'exit':
            $passId = $input['pass_id'] ?? null;
            $passNum = $input['pass_num'] ?? null;
            $guardId = $_SESSION['auth_user']['id'] ?? 1;

            if (!$passId && $passNum) {
                $stmtP = $db->prepare("SELECT id FROM gate_passes WHERE pass_number = ? LIMIT 1");
                $stmtP->execute([$passNum]);
                $passId = $stmtP->fetchColumn();
            }

            if (!$passId) {
                api_error('Pass identifier is required.');
            }

            // Update open gate log
            $stmtOut = $db->prepare("UPDATE gate_logs 
                SET check_out_time = NOW(), check_out_guard_id = ?, occupancy_status = 'Exited'
                WHERE pass_id = ? AND occupancy_status = 'Inside'");
            $stmtOut->execute([$guardId, $passId]);

            // Update pass status
            $stmtPassUp = $db->prepare("UPDATE gate_passes SET status = 'Used' WHERE id = ?");
            $stmtPassUp->execute([$passId]);

            // Audit Log
            $stmtAudit = $db->prepare("INSERT INTO audit_logs (actor_name, actor_role, action, record_ref, reason) VALUES (?, ?, ?, ?, ?)");
            $stmtAudit->execute([$_SESSION['auth_user']['name'] ?? 'Guard', 'Security', 'Exit recorded', (string)$passId, 'Visit closed']);

            api_response(['pass_id' => $passId], 'Exit recorded successfully.');
            break;

        /**
         * POST Record Gate Exception (Denied entry, invalid pass, emergency override)
         */
        case 'exception':
            $type = trim($input['type'] ?? '');
            $person = trim($input['person'] ?? '');
            $reason = trim($input['reason'] ?? '');
            $actor = $_SESSION['auth_user']['name'] ?? 'Guard';

            if ($type === '' || $reason === '') {
                api_error('Type and reason are required.');
            }

            $stmt = $db->prepare("INSERT INTO audit_logs (actor_name, actor_role, action, record_ref, reason) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$actor, 'Security', $type, $person ?: 'GATE-EXCEPTION', $reason]);

            api_response(['id' => $db->lastInsertId()], 'Exception recorded in audit log.');
            break;

        default:
            api_error("Unknown action: {$action}");
    }
} catch (Exception $e) {
    api_error($e->getMessage(), 500);
}
