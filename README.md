AWS SCAN

A cli to check compliance of AWS resources.

Example

./aws-scan scan --policy='public ssh|=|22|0.0.0.0/0' --policy='controller public|=|8090|0.0.0.0/0'
