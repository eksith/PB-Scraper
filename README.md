PB-Scraper
==========

Very simple pastebin.com scraper. Load index.html after uploading to your server. The iframe on the page will load s.php which will grab pastebin.com/archive and save each entry sorted by language/syntax.

This script expects a writable /data folder in root. Best to disable any execute permissions on this folder and never try to run any parsing scripts without proper filtering. Pastebin frequently contains viruses/malware pastes.

All saved pastes contain the title in the first line E.G. "Title: paste title" followed by several new lines.

DO NOT HAMMER PASTEBIN.COM WITH TOO MANY REQUESTS!

Requests are limited to 2 - 7 seconds and index.html loads s.php only every 4 minutes or so. I feel this is reasonable enough. Also, too many requests too soon may get your IP banned.
