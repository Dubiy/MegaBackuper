# Mega.nz backuper
Upload all your files to multiply free 50Gb mega.nz accounts.
If you have more than 50Gb, script use next account. Make "raid-0" in mega.nz :)


## Build

    docker build -t megabackuper .

## Config
    cd /path/to/folder/you/want/backup
    nano accounts

##### File format for "accounts"

    username_email1@gmail.com somepassword12345
    username_email2@gmail.com anotherpassword3442
    username_email3@gmail.com dumbpasswordsarebad
    username_email4@gmail.com strongpasswordhere
    accountEmail password
    ....

## Run

    cd /path/to/folder/you/want/backup
    docker run -v "$(pwd):$(pwd)" -w "$(pwd)" -t -i megabackuper php /var/pusher.php

`-v "$(pwd):$(pwd)"` - mount current directory to image (all data to backup)
`-w "$(pwd)"` - set mounted dir as current working dir
