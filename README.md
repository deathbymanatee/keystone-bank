# keystone-bank

## Installation 

1. Make sure you've installed [xampp](https://www.apachefriends.org/) server. 
After, navigate into the directory where xampp is installed. Open a terminal in this location. 
2. You will need to have some way to authenticate yourself before running anything Git related. GitHub deprecated password authentication
so you will probably need an SSH key to authenticate yourself. 
3. Once you're authenticated, run `git clone https://github.com/deathbymanatee/keystone-bank.git` in the `C:\\xampp\htdocs` directory. You will probably need 
administrative privileges to modify this directory. 
4. To test if everything installed correctly, start the xampp web server and type in `localhost/keystone-bank/HelloWorld.php` your browser and hit enter. You should see the contents of that file printed out on your screen

Here's the Google Drive folder with associated project documents: <https://drive.google.com/drive/folders/1xVr1QDkeqZqUSmWPWx2Dv1biAwcEAJ69> 

## Database Setup

1. Copy setup.sql into the directory where your mySQL binary lives. Open the XAMPP shell. 
2. Run the following command in your shell: 

```sql
source setup.sql
```

3. This script should set up the database with everything you need to start developing stuff and testing. 
