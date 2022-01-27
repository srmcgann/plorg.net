#!/bin/bash
cd /var/www/html/plorg.net/dist_public
php ipfs_db.sync.php
php $TEZOS_CLIENT_DIR/syncWallets.php
