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