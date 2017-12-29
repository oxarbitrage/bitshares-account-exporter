# BITSHARES ACCOUNT HISTORY EXPORTER

Demo: http://open-explorer.io/bitshares-account-exporter/index.php

The repo host an account exporter for bitshares, highly demanded by the community.

All bitshares related stuff is at `index.php`, the rest is just dependencies to make the form looks a bit prettier.

The exporter will use an ElasticSearch Wrapper instance(https://github.com/oxarbitrage/bitshares-es-wrapper) connected to a bitshares node with elasticsearch plugin to pull account operation history and present it in CSV format.
