AWS SCAN

A cli to check compliance of AWS resources.

Example

./aws-scan scan --policy='public ssh|=|22|0.0.0.0/0' --policy='controller public|=|8090|0.0.0.0/0'

#There are two ways to provide credential by priority

###Environment Variables

AWS_ACCESS_KEY_IDS
AWS_SECRET_ACCESS_KEY

###AWS credential file

~/.aws/credentials

It looks for default credential.
