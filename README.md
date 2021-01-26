# PHP Infector

A simple PHP script to inject PHP payloads into PHP files. 

If you have pwned some webserver, you can use this script to infect all the PHP files, so that if you lose access, you can obtain it again through the infected files.

With this you can:

- Protect it with a custom password (default: __7359__).
- Recursively inject your custom payload into all (or some) PHP files of the webserver. 
- Encode your payload with eval(base64_decode()) to prevent someone from reading it directly.
- Choose between injecting it at the beginning or the end of the file.
- Keep the original date of modification of the affected files to avoid being noticed (automatic).

## Comments

In some cases, you will find files whose execution ends in the middle of the code (with die(), header(), exit() or something like that). In these cases, if you inject the payload at the end of the file, it will not be executed. You must inject it at the beginning of the file. 

The script will always try to change the date of modification of the affected file to the original date. It will try to do so by using PHP and system functions, but sometimes it will not be able to do it. In such cases, you can try to do it manually.

The default path is the path in which the script is located. It will recursively list all the writable PHP files from this path. You can change this path to any other, as long as you have permissions.

## Usage

Simply upload it into the webserver, access it through it's URL, log in (default password is 7359), select the files in which you want to inject, write the payload (or use the default one) and click "Inject".

To change the default password, you need to sha1() the password you want to use and change $key on line 3.


![](https://i.imgur.com/MGVWbN8.png)

![](https://i.imgur.com/WnTo4K9.png)

