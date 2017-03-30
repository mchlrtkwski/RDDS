#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""Send email via smtp_host."""
import smtplib
from email.mime.text import MIMEText
from email.header    import Header
import sys

def sendAlert(receiver):
   ####smtp_host = 'smtp.live.com'        # microsoft
   ####smtp_host = 'smtp.gmail.com'       # google
   receiverName = receiver
   #f = open('../xdocuments/comments.txt', 'r')
   smtp_host = 'smtp-mail.outlook.com'  # yahoo
   login = 'rdds_alerts@outlook.com'
   password = '001001001m'
   recipients_emails = [receiverName]

   msg = MIMEText('Hello', 'plain', 'utf-8')
   msg['Subject'] = Header('Class Alert', 'utf-8')
   msg['From'] = login
   msg['To'] = ", ".join(recipients_emails)

   #f.close();

   s = smtplib.SMTP(smtp_host, 587, timeout=10)
   try:

      s.ehlo()
      s.starttls()
      s.ehlo()
      s.login(login, password)
      s.sendmail(msg['From'], recipients_emails, msg.as_string())
   finally:
      s.quit()



