#!/bin/bash
(crontab -l | grep -v "/usr/bin/php /home/ji3r0joamiqx/public_html/spot2delivery.com/artisan dm:disbursement") | crontab -
(crontab -l ; echo "00 10 * * * /usr/bin/php /home/ji3r0joamiqx/public_html/spot2delivery.com/artisan dm:disbursement") | crontab -
(crontab -l | grep -v "/usr/bin/php /home/ji3r0joamiqx/public_html/spot2delivery.com/artisan restaurant:disbursement") | crontab -
(crontab -l ; echo "00 11 * * * /usr/bin/php /home/ji3r0joamiqx/public_html/spot2delivery.com/artisan restaurant:disbursement") | crontab -
