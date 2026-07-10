<?php
// clean-logs.php (wrapper silencioso para el .sh)
@passthru('/bin/bash /home/master/applications/agfauhtwyt/public_html/clean-logs.sh >/dev/null 2>&1');

