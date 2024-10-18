-- 1.4.7-1.4.8

-- INDEXES

ALTER TABLE playsms_tblUser ADD INDEX username (username);
ALTER TABLE playsms_tblBilling ADD INDEX smslog_id (smslog_id);
ALTER TABLE playsms_tblBilling ADD INDEX uid (uid);
ALTER TABLE playsms_tblDLR ADD INDEX smslog_id (smslog_id);
ALTER TABLE playsms_tblDLR ADD INDEX uid (uid);
ALTER TABLE playsms_tblSMSInbox ADD INDEX in_uid (in_uid);
ALTER TABLE playsms_tblSMSIncoming ADD INDEX in_uid (in_uid);
ALTER TABLE playsms_tblSMSOutgoing ADD INDEX p_datetime (p_datetime);
ALTER TABLE playsms_tblSMSOutgoing ADD INDEX p_smsc (p_smsc);
ALTER TABLE playsms_tblSMSOutgoing ADD INDEX queue_code (queue_code);
ALTER TABLE playsms_tblSMSOutgoing ADD INDEX uid (uid);
ALTER TABLE playsms_tblSMSOutgoing_queue ADD INDEX flag (flag);
ALTER TABLE playsms_tblSMSOutgoing_queue ADD INDEX uid (uid);
ALTER TABLE playsms_tblSMSOutgoing_queue_dst ADD INDEX queue_id (queue_id);
ALTER TABLE playsms_featureSendfromfile ADD INDEX sid (sid);
ALTER TABLE playsms_featurePhonebook_group_contacts ADD INDEX pid (pid) ;
ALTER TABLE playsms_featurePhonebook_group_contacts ADD INDEX gpid (gpid) ;
