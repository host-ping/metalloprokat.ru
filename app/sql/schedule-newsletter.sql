SET @newsletter_id = 8;

INSERT IGNORE INTO newsletter_recipient (newsletter_id, subscriber_id)
  SELECT @newsletter_id, ID FROM UserSend us
    JOIN User u ON (us.User_ID = u.User_ID OR us.Email = u.Email)
    JOIN Message75 c ON u.ConnectCompany = c.Message_ID
  WHERE c.code_access > 0;

