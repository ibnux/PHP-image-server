# PHP Image Server

this image server created for managing image uploaded from my apps

# REQUIREMENTS

- [LAMP](https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-ubuntu-18-04)
- PHP 5 Above
- PHP GD

# INSTALATION

- Upload project to webserver
- make folder **r** and **i** writeable, chmod 777
- run **[composer](https://getcomposer.org/download/) install**
- create database
- import **db.sql**
- copy **config.example.php** to **config.php**
- edit **config.php**

# HOW TO

POST **multipart/form-data** images with **photo** parameter

for authorization using JWT use **x-access-token** header and need parameter UserId

    curl --location --request POST 'http://images.ibnux.org/' \
    --header 'Content-Type: multipart/form-data' \
    --header 'x-access-token: jwt.token.sign' \
    --form 'photo=@/Users/ibnux/Pictures/photo6199512548924041707.jpg'
Success
    
    {"status":"success","data":"i\/1\/1.jpg"}
Failed

    {"status":"failed","message":"Nothing to upload"}
    
download

    http://images.ibnux.org/i/1/1.jpg
    
resize
    
    /** 
    * /r/w/h/m/i/file.jpg
    * resize
    * width
    * height
    * mode
    * 0 proportional
    * 1 crop
    * 2 square from width
    * 
    * i/1/1.jpg
    * crop 512
    * r/512/512/1/i/1/1.jpg
    */

result

    http://images.ibnux.org/r/512/512/1/i/1/1.jpg

# LICENSE
## Apache License 2.0

Permissions

    ✓ Commercial use  
    ✓ Distribution  
    ✓ Modification  
    ✓ Patent use  
    ✓ Private use  
  
Conditions  
  
    License and copyright notice  
    State changes  
  
Limitations  
  
    No Liability  
    No Trademark use  
    No Warranty  
  
you can find license file inside folder
