<?php
/* Mock data for the UI prototype. Replace with database queries when the Laravel/MySQL backend is wired in. */

$ROLES = [
  'super_admin' => 'Super Admin',
  'org_admin'   => 'Organization Admin',
  'receptionist'=> 'Receptionist',
  'host'        => 'Host / Employee',
  'approver'    => 'Department Head / Approver',
  'security'    => 'Security Guard',
  'stores'      => 'Stores / Material Officer',
  'contractor_mgr' => 'Contractor Manager',
  'ehs'         => 'EHS / Safety Officer',
  'auditor'     => 'Auditor / Management',
];

$SITES = ['Pune Plant', 'Mumbai HQ', 'Nashik Warehouse', 'Chakan Factory'];

$VISITORS = [
  ['id'=>'V-1001','name'=>'Rohan Deshmukh','company'=>'Tata Projects','category'=>'Customer','phone'=>'98220 41177','email'=>'rohan.d@tataprojects.example','visits'=>6,'last'=>'24 Oct 2026','flag'=>''],
  ['id'=>'V-1002','name'=>'Meera Iyer','company'=>'Bosch Rexroth','category'=>'Supplier','phone'=>'98907 22314','email'=>'meera.iyer@boschr.example','visits'=>11,'last'=>'26 Oct 2026','flag'=>''],
  ['id'=>'V-1003','name'=>'Imran Shaikh','company'=>'SwiftCourier','category'=>'Delivery person','phone'=>'90110 55621','email'=>'-','visits'=>43,'last'=>'26 Oct 2026','flag'=>''],
  ['id'=>'V-1004','name'=>'Kavita Joshi','company'=>'Self','category'=>'Interview candidate','phone'=>'97300 18842','email'=>'kavita.j@mail.example','visits'=>1,'last'=>'26 Oct 2026','flag'=>''],
  ['id'=>'V-1005','name'=>'Anil Pawar','company'=>'Pawar Engineering','category'=>'Vendor','phone'=>'99600 71230','email'=>'anil@pawareng.example','visits'=>8,'last'=>'19 Oct 2026','flag'=>'watch'],
  ['id'=>'V-1006','name'=>'Sunita Kulkarni','company'=>'Self','category'=>'Personal guest','phone'=>'98500 66012','email'=>'-','visits'=>2,'last'=>'12 Oct 2026','flag'=>''],
  ['id'=>'V-1007','name'=>'David Fernandes','company'=>'Siemens Ltd','category'=>'Official','phone'=>'98190 33071','email'=>'david.f@siemens.example','visits'=>5,'last'=>'22 Oct 2026','flag'=>''],
  ['id'=>'V-1008','name'=>'Prakash Nair','company'=>'Nair Logistics','category'=>'Supplier','phone'=>'94220 80915','email'=>'prakash@nairlog.example','visits'=>17,'last'=>'25 Oct 2026','flag'=>''],
];

$APPOINTMENTS = [
  ['ref'=>'APT-26-0412','visitor'=>'Rohan Deshmukh (+2)','company'=>'Tata Projects','host'=>'Sneha Kapoor','dept'=>'Projects','site'=>'Pune Plant','when'=>'26 Oct, 11:00','purpose'=>'Site survey','status'=>'Approved','group'=>3],
  ['ref'=>'APT-26-0413','visitor'=>'Meera Iyer','company'=>'Bosch Rexroth','host'=>'Vikram Shah','dept'=>'Procurement','site'=>'Pune Plant','when'=>'26 Oct, 11:30','purpose'=>'Quarterly review','status'=>'Checked in','group'=>1],
  ['ref'=>'APT-26-0414','visitor'=>'Kavita Joshi','company'=>'Self','host'=>'Neha Rane','dept'=>'HR','site'=>'Mumbai HQ','when'=>'26 Oct, 14:00','purpose'=>'Interview','status'=>'Approved','group'=>1],
  ['ref'=>'APT-26-0415','visitor'=>'David Fernandes (+1)','company'=>'Siemens Ltd','host'=>'Arjun Menon','dept'=>'Maintenance','site'=>'Chakan Factory','when'=>'26 Oct, 15:30','purpose'=>'Panel inspection','status'=>'Pending approval','group'=>2],
  ['ref'=>'APT-26-0416','visitor'=>'Sunita Kulkarni','company'=>'Self','host'=>'Vikram Shah','dept'=>'Procurement','site'=>'Pune Plant','when'=>'27 Oct, 10:00','purpose'=>'Personal visit','status'=>'Info requested','group'=>1],
  ['ref'=>'APT-26-0417','visitor'=>'Prakash Nair','company'=>'Nair Logistics','host'=>'Stores Dept','dept'=>'Stores','site'=>'Nashik Warehouse','when'=>'27 Oct, 09:00','purpose'=>'Freight contract','status'=>'Rescheduled','group'=>1],
  ['ref'=>'APT-26-0409','visitor'=>'Anil Pawar','company'=>'Pawar Engineering','host'=>'Arjun Menon','dept'=>'Maintenance','site'=>'Pune Plant','when'=>'25 Oct, 10:00','purpose'=>'Quotation','status'=>'No-show','group'=>1],
];

$APPROVALS = [
  ['id'=>'AP-7781','type'=>'Visitor','subject'=>'David Fernandes (+1) – Panel inspection','requester'=>'Arjun Menon','site'=>'Chakan Factory','level'=>'1 of 2','age'=>'35 min','due'=>'on time'],
  ['id'=>'AP-7782','type'=>'Material outward','subject'=>'OUT-0291 · CNC spindle motor for repair','requester'=>'Maintenance','site'=>'Pune Plant','level'=>'2 of 3','age'=>'2 h 10 min','due'=>'on time'],
  ['id'=>'AP-7783','type'=>'Contractor','subject'=>'Shree Electricals – 6 workers, shutdown work','requester'=>'Contractor Mgr','site'=>'Pune Plant','level'=>'1 of 2','age'=>'1 day','due'=>'escalated'],
  ['id'=>'AP-7784','type'=>'Walk-in','subject'=>'Vishal More – Vendor, steel samples','requester'=>'Reception','site'=>'Pune Plant','level'=>'1 of 1','age'=>'4 min','due'=>'on time'],
  ['id'=>'AP-7785','type'=>'Material inward','subject'=>'IN-0554 · Hydraulic hoses (PO 44021)','requester'=>'Stores','site'=>'Nashik Warehouse','level'=>'1 of 2','age'=>'5 h','due'=>'reminder sent'],
];

$ONSITE = [
  ['pass'=>'GP-26-08812','name'=>'Meera Iyer','company'=>'Bosch Rexroth','host'=>'Vikram Shah','gate'=>'Gate 1','in'=>'11:34','duration'=>'0 h 42 m','type'=>'Visitor'],
  ['pass'=>'GP-26-08807','name'=>'Imran Shaikh','company'=>'SwiftCourier','host'=>'Stores Dept','gate'=>'Gate 2','in'=>'09:52','duration'=>'2 h 24 m','type'=>'Delivery'],
  ['pass'=>'GP-26-08799','name'=>'Ramesh Gaikwad','company'=>'Shree Electricals','host'=>'Maintenance','gate'=>'Gate 2','in'=>'07:15','duration'=>'5 h 01 m','type'=>'Contractor'],
  ['pass'=>'GP-26-08800','name'=>'Santosh Bhosale','company'=>'Shree Electricals','host'=>'Maintenance','gate'=>'Gate 2','in'=>'07:16','duration'=>'5 h 00 m','type'=>'Contractor'],
  ['pass'=>'GP-26-08804','name'=>'Prakash Nair','company'=>'Nair Logistics','host'=>'Stores Dept','gate'=>'Gate 2','in'=>'08:40','duration'=>'3 h 36 m','type'=>'Visitor'],
];

$INWARD = [
  ['id'=>'IN-0554','supplier'=>'Hydro-Flex Pvt Ltd','ref'=>'PO 44021 / CH-9921','vehicle'=>'MH12 QT 4410','items'=>'Hydraulic hoses ×40','dept'=>'Maintenance','status'=>'Pending approval','received'=>'—'],
  ['id'=>'IN-0553','supplier'=>'Bosch Rexroth','ref'=>'PO 43990 / INV-2231','vehicle'=>'MH14 GH 2087','items'=>'Servo valves ×6','dept'=>'Production','status'=>'Received – shortage','received'=>'5 of 6'],
  ['id'=>'IN-0552','supplier'=>'SwiftCourier','ref'=>'AWB 77120041','vehicle'=>'Two-wheeler','items'=>'Sample cartons ×3','dept'=>'Quality','status'=>'Received','received'=>'3 of 3'],
  ['id'=>'IN-0551','supplier'=>'Pawar Engineering','ref'=>'Repair return RT-118','vehicle'=>'MH12 AB 3302','items'=>'Gearbox (repaired)','dept'=>'Maintenance','status'=>'Received – damage','received'=>'1 of 1'],
];

$OUTWARD = [
  ['id'=>'OUT-0291','requester'=>'Maintenance','dest'=>'Pawar Engineering, Chakan','items'=>'CNC spindle motor ×1','kind'=>'Returnable','due'=>'09 Nov 2026','status'=>'Pending approval'],
  ['id'=>'OUT-0290','requester'=>'Production','dest'=>'Customer – Tata Motors','items'=>'Finished gear sets ×120','kind'=>'Non-returnable','due'=>'—','status'=>'Released'],
  ['id'=>'OUT-0288','requester'=>'Quality','dest'=>'NABL Lab, Pune','items'=>'Test samples ×24','kind'=>'Returnable','due'=>'30 Oct 2026','status'=>'Released'],
  ['id'=>'OUT-0285','requester'=>'Stores','dest'=>'Scrap dealer – Mehta Metals','items'=>'MS scrap 1,850 kg','kind'=>'Non-returnable','due'=>'—','status'=>'Approved'],
  ['id'=>'OUT-0281','requester'=>'Maintenance','dest'=>'Calibration vendor','items'=>'Torque wrenches ×8','kind'=>'Returnable','due'=>'18 Oct 2026','status'=>'Overdue'],
];

$RETURNS = [
  ['pass'=>'OUT-0281','item'=>'Torque wrenches','issued'=>8,'returned'=>5,'unit'=>'nos','owner'=>'Arjun Menon (Maintenance)','due'=>'18 Oct 2026','state'=>'Overdue'],
  ['pass'=>'OUT-0288','item'=>'Test samples','issued'=>24,'returned'=>0,'unit'=>'nos','owner'=>'Quality Lab','due'=>'30 Oct 2026','state'=>'Open'],
  ['pass'=>'OUT-0274','item'=>'Welding rectifier','issued'=>2,'returned'=>1,'unit'=>'nos','owner'=>'Plant Engineering','due'=>'02 Nov 2026','state'=>'Partial'],
  ['pass'=>'OUT-0269','item'=>'Pressure gauges','issued'=>12,'returned'=>12,'unit'=>'nos','owner'=>'Utilities','due'=>'20 Oct 2026','state'=>'Closed'],
];

$CONTRACTORS = [
  ['company'=>'Shree Electricals','rep'=>'Ganesh Patil','workers'=>6,'project'=>'Shutdown – Line 3','site'=>'Pune Plant','wo'=>'WO/26/3318','valid'=>'31 Dec 2026','docs'=>'Valid','induction'=>'Complete'],
  ['company'=>'Kalpataru Civil','rep'=>'Sachin More','workers'=>18,'project'=>'Warehouse extension','site'=>'Nashik Warehouse','wo'=>'WO/26/3190','valid'=>'15 Nov 2026','docs'=>'Expiring in 20 days','induction'=>'Complete'],
  ['company'=>'CleanServe Facilities','rep'=>'Farida Khan','workers'=>11,'project'=>'Housekeeping','site'=>'Mumbai HQ','wo'=>'WO/26/2874','valid'=>'31 Mar 2027','docs'=>'Valid','induction'=>'Pending (2)'],
  ['company'=>'Apex Scaffolding','rep'=>'Dinesh Rao','workers'=>9,'project'=>'Paint shop revamp','site'=>'Chakan Factory','wo'=>'WO/26/3402','valid'=>'12 Oct 2026','docs'=>'Expired','induction'=>'Complete'],
];

$WORKERS = [
  ['name'=>'Ramesh Gaikwad','company'=>'Shree Electricals','id'=>'Aadhaar ••• 4410','trade'=>'Electrician','medical'=>'14 Mar 2027','status'=>'Inside'],
  ['name'=>'Santosh Bhosale','company'=>'Shree Electricals','id'=>'Aadhaar ••• 9021','trade'=>'Helper','medical'=>'14 Mar 2027','status'=>'Inside'],
  ['name'=>'Pravin Thorat','company'=>'Apex Scaffolding','id'=>'Aadhaar ••• 1187','trade'=>'Rigger','medical'=>'02 Oct 2026','status'=>'Blocked'],
  ['name'=>'Salim Ansari','company'=>'Kalpataru Civil','id'=>'Aadhaar ••• 7742','trade'=>'Mason','medical'=>'19 Jan 2027','status'=>'Outside'],
];

$VEHICLES = [
  ['reg'=>'MH12 QT 4410','type'=>'Truck (LCV)','driver'=>'Sanjay Kamble','link'=>'IN-0554 (material)','gate'=>'Gate 2','in'=>'10:12','out'=>'—','parking'=>'Bay B-4','permit'=>'Valid'],
  ['reg'=>'MH14 KR 5521','type'=>'Car','driver'=>'Meera Iyer','link'=>'GP-26-08812 (visitor)','gate'=>'Gate 1','in'=>'11:34','out'=>'—','parking'=>'Visitor P-2','permit'=>'Valid'],
  ['reg'=>'MH12 AB 3302','type'=>'Tempo','driver'=>'Ajay Shinde','link'=>'IN-0551 (material)','gate'=>'Gate 2','in'=>'08:05','out'=>'09:40','parking'=>'Bay B-1','permit'=>'Valid'],
  ['reg'=>'MH15 ZZ 0098','type'=>'Truck','driver'=>'Unknown','link'=>'—','gate'=>'Gate 2','in'=>'—','out'=>'—','parking'=>'—','permit'=>'Expired'],
];

$WATCHLIST = [
  ['name'=>'Anil Pawar','company'=>'Pawar Engineering','reason'=>'Unauthorized photography on shop floor, Sept 2026','by'=>'Plant Head','added'=>'30 Sep 2026','expires'=>'30 Dec 2026','action'=>'Warn only'],
  ['name'=>'Vishal More','company'=>'Self','reason'=>'Previous gate pass misuse','by'=>'Security Manager','added'=>'11 Aug 2026','expires'=>'11 Nov 2026','action'=>'Deny entry'],
  ['name'=>'Rakesh Dubey','company'=>'Dubey Traders','reason'=>'Dispute – legal notice pending','by'=>'Org Admin','added'=>'02 Jun 2026','expires'=>'Review due','action'=>'Warn only'],
];

$AUDIT = [
  ['time'=>'26 Oct 10:41:08','actor'=>'Vikram Shah','role'=>'Approver','action'=>'Approved','ref'=>'APT-26-0413','reason'=>'Known supplier'],
  ['time'=>'26 Oct 10:22:51','actor'=>'Guard – Gate 2','role'=>'Security','action'=>'Timestamp corrected','ref'=>'GP-26-08804','reason'=>'Scanner offline, entry logged late'],
  ['time'=>'26 Oct 09:58:30','actor'=>'Neha Rane','role'=>'Host','action'=>'Rescheduled','ref'=>'APT-26-0417','reason'=>'Visitor request'],
  ['time'=>'26 Oct 09:15:03','actor'=>'Plant Head','role'=>'Org Admin','action'=>'Emergency override','ref'=>'GP-26-08790','reason'=>'Fire inspector, approval pending'],
  ['time'=>'25 Oct 17:40:12','actor'=>'Stores Officer','role'=>'Stores','action'=>'Return reconciled (partial)','ref'=>'OUT-0281','reason'=>'5 of 8 returned'],
  ['time'=>'25 Oct 16:05:44','actor'=>'Security Manager','role'=>'Org Admin','action'=>'Pass revoked','ref'=>'GP-26-08751','reason'=>'Visitor left site early'],
];

$VISIT_TREND = ['Mon'=>142,'Tue'=>158,'Wed'=>171,'Thu'=>149,'Fri'=>186,'Sat'=>64,'Sun'=>22];
