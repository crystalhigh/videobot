<h3>Video Popup Project</h3>


## How to install the Project

1. Configure database
  - Create database
  - Migrate the tables
     php artisan migrate
     php artisan db:seed

2. Install jwt authentication
  https://jwt-auth.readthedocs.io/en/develop/laravel-installation/

3. Install laravel-admin
  https://github.com/z-song/laravel-admin

4. How to run the project

composer install

npm install

npm run dev (watch or prod)

php artisan serve

## Autoresponders Integrations

<h3>Getting Started</h3>

Currently we have 2 different types of integrations.
    
The first type of autoresponders requires an API key where users can find it in their autoresponder settings. Objects of this type must be created on top of `App\Utils\Autoresponder\Driver`. Example of API key based autoresponder: GetResponse, Sendinblue, and MailChimp.
    
The 2nd type of autoresponders requires an access token where users have to go through an OAuth 2.0 authorization and grant our app access to their accounts. Objects of this type must be created on top of `App\Utils\Autoresponder\WithOAuthDriver`. Example of OAuth 2.0 based autoresponders: Aweber.

Please note that Zapier and Pabbly are not autoresponders and they don't require the user to authorize their accounts.
All what they have to do is to insert their Zapier Zap's Webhook URL or Pabbly Workflow's Webhook URL.

### A special case with MailChimp

MailChimp hosts their user accounts in different servers/datacenters thus obliged to include the user account datacenter in every API call, exactly at the beginning of the API endpoint. Example: `https://<dc>.api.mailchimp.com/3.0/` by changing `<dc>` to the user&rsquo;s account datacenter.

Mailchimp Documentation <a href="https://mailchimp.com/developer/marketing/docs/fundamentals/">https://mailchimp.com/developer/marketing/docs/fundamentals/</a>

### OAuth 2.0 based autoresponders authentication

GET - /integrations/oauth/{autoresponder}

Authorise an autoresponder.

<h5>Path Parameters</h5>

<table>
  <thead>
    <tr>
      <td>Parameters</td>
      <td>Description</td>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>autoresponder (required)</td>
      <td>An OAuth 2.0 based autoresponder</td>
    </tr>
  </tbody>
</table>

### Test autoresponders API

#### API Key based autoresponders

POST - /api/integrations/{autoresponder}/test

Authorise an autoresponder.

##### Path Parameters

<table>
  <thead>
    <tr>
      <td>Parameters</td>
      <td>Description</td>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>autoresponder (required)</td>
      <td>An API Key based autoresponder</td>
    </tr>
  </tbody>
</table>

##### Request Body (application/json)

<table>
  <thead>
    <tr>
      <td>Parameters</td>
      <td>Description</td>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>api_key (required)</td>
      <td>The autoresponder&rsquo;s user API Key.</td>
    </tr>
  </tbody>
</table>

#### OAuth 2.0 based autoresponders

POST - /api/integrations/{autoresponder}/test

##### Path Parameters

<table>
  <thead>
    <tr>
      <td>Parameters</td>
      <td>Description</td>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>autoresponder (required)</td>
      <td>An OAuth 2.0 based autoresponder</td>
    </tr>
  </tbody>
</table>

##### Request Body (application/json)

<table>
  <thead>
    <tr>
      <td>Parameters</td>
      <td>Description</td>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>access_token (required)</td>
      <td>The access token.</td>
    </tr>
    <tr>
      <td>refresh_token (required)</td>
      <td>
        <p>The refresh token.</p>
      </td>
    </tr>
    <tr>
      <td>expires_in (required)</td>
      <td>
        <p>The access token expiry in seconds.</p>
      </td>
    </tr>
  </tbody>
</table>

### Test Autoresponders Authorization

If we don't know the authentication method of the specified autoresponder, we should do some checking before calling the `testApi()` method:

```php
$response = null;
$autoresponder = Autoresponder::factory('SOME_AUTORESPONDER');

if ($autoresponder->withOAuth()) {
    $autoresponder
        ->setAccessToken(request('access_token'))
        ->setRefreshToken(request('refresh_token'))
        ->setExpiresIn(request('expires_in'));
} else {
    $autoresponder->setApiKey(request('api_key'));

    if ($autoresponder instanceof Mailchimp) {
        $autoresponder->setDatacenter(request('datacenter'));
    }
}

try {
    $response = $autoresponder->testApi();
} catch (Throwable $e) {
    if ($e instanceof RequestException) {
        dump($e->response->body());

        return;
    }

    throw $e;
}

dump($response);
```

But, if we know the authentication method plus the autoresponder, we can just pass the parameters to the factory method:

```php
$response = null;
$params = [
  'access_token' => 'ABCD',
  'access_token' => null,
  'access_token' => 0,
  'datacenter' => 'usX', // only for Mailchimp
];
$autoresponder = Autoresponder::factory('SOME_AUTORESPONDER', $params);

try {
    $response = $autoresponder->testApi();
} catch (Throwable $e) {
    if ($e instanceof RequestException) {
        dump($e->response->body());

        return;
    }

    throw $e;
}

dump($response);
```

### Get lists

Get lists of the user from an autoresponder

```php
$lists = null;
$autoresponder = Autoresponder::factory('SOME_AUTORESPONDER', $params);

try {
    $lists = $autoresponder->getLists();
} catch (Throwable $e) {
    if ($e instanceof RequestException) {
        dump($e->response->body());

        return;
    }

    throw $e;
}

dump('Lists:');
dump($lists);
```

### Add a new subscriber

Add a new subscriber to a user's autoresponder list.

```php
$added = false;
$autoresponder = Autoresponder::factory('SOME_AUTORESPONDER', $params);

try {
    $added = $autoresponder->addSubscriber('list_id', 'email', 'first_name', 'last_name');
} catch (Throwable $e) {
    if ($e instanceof RequestException) {
        dump($e->response->body());

        return;
    }

    throw $e;
}

if ($added) {
  dump('Subscriber added successfully!');
} else {
  dump('Error: the subscriber was not added!');
}

```

### Webhook Integration

Zapier & Pabbly are not autoresponders, but they are data automation tools. They provide a nice way to send data to a flow with Webhooks.

Here's how you can send a subscriber's data to a Zapier or Pabbly Webook so it can catch it(Catch Hook) and do a lot of automation afterward.

```php
$webhook = new Webhook(
    'THE_USER_WEBHOOK_URL'
);

try {
    $isSent = $zapier->post('subscriber_email', 'subscriber_first_name', 'subscriber_last_name');
} catch (\Throwable $e) {
    if ($e instanceof RequestException) {
        dump($e->response->body());

        return;
    }

    throw $e;
}
```
