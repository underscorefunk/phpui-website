# PHPUI Website

This website is hosted on Cloudways with its source located on github.

Deploying github->Cloudways
https://www.cloudways.com/blog/deploy-php-application/

Automating the deployment
https://support.cloudways.com/en/articles/5124785-automatically-deploy-from-git-to-server-using-webhooks

The deploy is setup with a script located in the root of the app. A .env file was created in /private_html/ on cloudways, which is a sibling of /public_html/. Cloudways doesn't support setting environment variables yet, so this is our best bet. A .htaccess file is tossed in the private_html folder for good measure.

/root
    /private_html/
        .htaccess
        .env
    /public_html/
        ...

We don't want just anybody to be able to trigger deploys so I've added a deploy key to the mix.




