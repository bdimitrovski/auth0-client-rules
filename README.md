Auth0 Client Rules
========================

This small Symfony app lists all the clients and rules associated with them. If you've used Auth0 before (https://auth0.com/), you should already be familiar with the concept of rules.

By default, all Auth0 rules are applied to all client applications. Sometimes, we need to apply only some rules to some applications. This app will help you do that.

![alt tag](http://g.recordit.co/akTTFCnMAz.gif)

Usage & requirements
--------------

Since this example app uses Docker, you will need to have it installed on your system - download and install it from here: https://www.docker.com/. After you clone this repo, take the following steps:

  * Run ```make build && make up && make install``` from the project.

  * Once the container is built, follow the steps from here https://auth0.com/docs/quickstart/webapp/symfony to configure the app with your data (you will need to be logged in to your Auth0 account to see pre-populated data).

  * Make sure you create a valid token for calling the Auth0 Management APIv2: https://auth0.com/docs/api/management/v2/tokens - replace the following with your specific data in ```src/Controller/ClientRulesController.php```:

  ``` const AUDIENCE = <YOUR_AUDIENCE_URL>
  const DOMAIN = <YOUR_AUTH0_DOMAIN>
  const CLIENT_ID = <YOUR_CLIENT_ID>
  const CLIENT_SECRET = <YOUR_CLIENT_SECRET>
  ```

  * Once you're done, run the app at http://localhost:5500/clients/rules and login - you should then see all the rules for all clients.

  * That's it! Make sure you have allowed only specific users to access the client by using the whitelist rule from your app (https://manage.auth0.com/#/rules). It should look something like this:

```javascript
  function (user, context, callback) {
    var whitelist = [ 'AllowedUser1@gmail.com', 'AnotherOne@somemail.com' ];  //authorized users
    var userHasAccess = whitelist.some(
      function (email) {
        return email === user.email;
      });

    if (!userHasAccess) {
      return callback(new UnauthorizedError('Access denied.'));
    }

  }
    callback(null, user, context);
}
```

Prerequisites
--------------

In order to display rules for some clients only, you have to configure your rules like this:

```javascript
  function (user, context, callback) {
    if (context.clientName === 'MyAppToWhiteList' || context.clientName === 'AnotherAppToWhiteList' || context.clientID === '123456789') {
       // Your rule logic
     }
      callback(null, user, context);
}
```

You can do it either by ```clientID``` or ```clientName``` so it's really easy to do it in any fashion you like.

Contributions
--------------

Feel free to fork the repo and create PR with improvements.
