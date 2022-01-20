#!/bin/bash
rm -rf /home/cantelope/.tezos-node/context
rm -rf /home/cantelope/.tezos-node/store
rm /home/cantelope/.tezos-node/lock
mkdir /home/cantelope/tezos
cd /home/cantelope/tezos
rm tezos-mainnet.rolling
wget https://mainnet.xtz-shots.io/rolling -O tezos-mainnet.rolling
tezos-node snapshot import tezos-mainnet.rolling

