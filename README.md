## ![](http://i.imgur.com/cacgQlq.png)  Pinterest API - Laravel
-------------------

A Package for using official [Pinterest API](https://dev.pinterest.com) with Laravel.

# Requirements
- PHP 5.4 or higher
- Laravel 5.0 or Above
- cURL
- Registered Pinterest App

# Get started
To use the Pinterest API you have to register yourself as a developer and [create](https://dev.pinterest.com/apps/) an application. After you've created your app you will receive a `app_id` and `app_secret`.

> The terms `client_id` and `client_secret` are in this case `app_id` and `app_secret`.

## Installation
The Pinterest API wrapper is available on Composer.

```
composer require waleedahmad/laravel-pinterest
```

## Configuration
Add service provider for `$providers[]` array in `config/app.php` array.
```php
'providers' => [
    [...] // other service providers
    \WaleedAhmad\Pinterest\ServiceProviders\PinterestServiceProvider::class,
]
```
Run `vendor:publish` command to copy pinterest configuration to app configs directory.
```$xslt
php artisan vendor:publish --provider="WaleedAhmad\Pinterest\ServiceProviders\PinterestServiceProvider"
```

Update `.env` file and fill in these `env` variables. 
```
PINTEREST_KEY=YOUR_APP_KEY
PINTEREST_SECRET=YOUR_APP_SECRET
PINTEREST_REDIRECT_URI=YOUR_CALLBACK_URL
```

## Getting access token in exchange for code
After you have initialized the class you can get a login URL:

```php
$loginurl = Pinterest::auth()->getLoginUrl(CALLBACK_URL, array('read_public'));
echo '<a href=' . $loginurl . '>Authorize Pinterest</a>';
```

Check the [Pinterest documentation](https://dev.pinterest.com/docs/api/overview/#scopes) for the available scopes.

After your user has used the login link to authorize he will be send back to the given `CALLBACK_URL`. The URL will contain the `code` which can be exchanged into an `access_token`. To exchange the code for an `access_token` and set it you can use the following code:

```php
if(isset($_GET["code"])){
    $token = Pinterest::auth()->getOAuthToken($_GET["code"]);
    Pinterest::auth()->setOAuthToken($token->access_token);
}
```

## Get the user's profile

To get the profile of the current logged in user you can use the `Users::me(<array>);` method.

```php
$me = Pinterest::user()->me();
echo $me;
```

# Models
The API wrapper will parse all data through it's corresponding model. This results in the possibility to (for example) directly `echo` your model into a JSON string.

Models also show the available fields (which are also described in the Pinterest documentation). By default, not all fields are returned, so this can help you when providing extra fields to the request.

## Available models

### [User](https://dev.pinterest.com/docs/api/users/#user-object)

### [Pin](https://dev.pinterest.com/docs/api/pins/#pin-object)

### [Board](https://dev.pinterest.com/docs/api/boards/#board-object)

### Interest
- id
- name

## Retrieving extra fields
If you want more fields you can specify these in the `$data` (GET requests) or `$fields` (PATCH requests) array. Example:

```php
Pinterest::user()->me();
```

Response:

```json
{
    "id": "503066358284560467",
    "username": null,
    "first_name": "Waleed",
    "last_name": "Ahmad",
    "bio": null,
    "created_at": null,
    "counts": null,
    "image": null
}
```

By default, not all fields are returned. The returned data from the API has been parsed into the `User` model. Every field in this model can be filled by parsing an extra `$data` array with the key `fields`. Say we want the user's username, first_name, last_name and image (small and large):

```php
Pinterest::user()->me(array(
    'fields' => 'username,first_name,last_name,image[small,large]'
));
```

The response will now be:

```json
{
    "id": "503066358284560467",
    "username": "waleedahmad",
    "first_name": "Waleed",
    "last_name": "Ahmad",
    "bio": null,
    "created_at": null,
    "counts": null,
    "image": {
        "small": {
                "url": "http://media-cache-ak0.pinimg.com/avatars/waleedahmad_1438089829_30.jpg",
                "width": 30,
                "height": 30
            },
            "large": {
                "url": "http://media-cache-ak0.pinimg.com/avatars/waleedahmad_1438089829_280.jpg",
                "width": 280,
                "height": 280
            }
        }
    }
}
```

# Collection

When the API returns multiple models (for instance when your requesting the pins from a board) the wrapper will put those into a `Collection`.

The output of a collection contains the `data` and page `key`. If you echo the collection you will see a json encoded output containing both of these. Using the collection as an array will only return the items from `data`.

Available methods for the collection class:

## Get all items
`all()`

```php
$pins = Pinterest::user()->getMePins();
$pins->all();
```

Returns: `array<Model>`

## Get item at index
`get( int $index )`

```php
$pins = Pinterest::user()->getMePins();
$pins->get(0);
```

Returns: `Model`

## Check if collection has next page

`hasNextPage()`

```php
$pins = Pinterest::user()->getMePins();
$pins->hasNextPage();
```

Returns: `Boolean`

# Available methods

> Every method containing a `data` array can be filled with extra data. This can be for example extra fields or pagination.

## Authentication

The methods below are available through `Pinterest::auth`.

### Get login URL
`getLoginUrl(string $redirect_uri, array $scopes, string $response_type = "code");`

```php
Pinterest::auth()->getLoginUrl("https://pinterest.dev/callback.php", array("read_public"));
```

Check the [Pinterest documentation](https://dev.pinterest.com/docs/api/overview/#scopes) for the available scopes.

**Note: since 0.2.0 the default authentication method has changed to `code` instead of `token`. This means you have to exchange the returned code for an access_token.**

### Get access_token
`getOAuthToken( string $code );`

```php
Pinterest::auth()->getOAuthToken($code);
```

### Set access_token
`setOAuthToken( string $access_token );`

```php
Pinterest::auth()->setOAuthToken($access_token);
```

### Get state
`getState();`

```php
Pinterest::auth()->getState();
```

Returns: `string`

### Set state
`setState( string $state );`

This method can be used to set a state manually, but this isn't required since the API will automatically generate a random state on initialize.

```php
Pinterest::auth()->setState($state);
```

## Rate limit

### Get limit
`getRateLimit();`

This method can be used to get the maximum number of requests.

```php
Pinterest::getRateLimit();
```

Returns: `int`

### Get remaining
`getRateLimitRemaining();`

This method can be used to get the remaining number of calls.

```php
Pinterest::getRateLimitRemaining();
```

Returns: `int`

## Users

The methods below are available through `Pinterest::users`.

> You also cannot access a user’s boards or Pins who has not authorized your app.

### Get logged in user
`me( array $data );`

```php
Pinterest::user()->me();
```

Returns: `User`

### Find a user
`find( string $username_or_id );`

```php
Pinterest::user()->find('waleedahmad');
```

Returns: `User`

### Get user's pins
`getMePins( array $data );`

```php
Pinterest::user()->getMePins();
```

Returns: `Collection<Pin>`

### Search in user's pins
`getMePins( string $query, array $data );`

```php
Pinterest::user()->searchMePins("cats");
```

Returns: `Collection<Pin>`

### Search in user's boards
`searchMeBoards( string $query, array $data );`

```php
Pinterest::user()->searchMeBoards("cats");
```

Returns: `Collection<Board>`

### Get user's boards
`getMeBoards( array $data );`

```php
Pinterest::user()->getMeBoards();
```

Returns: `Collection<Board>`

### Get user's followers
`getMeFollowers( array $data );`

```php
Pinterest::user()->getMeFollowers();
```

Returns: `Collection<Pin>`

## Boards

The methods below are available through `Pinterest::boards`.

### Get board
`get( string $board_id, array $data );`

```php
Pinterest::boards()->get("waleedahmad/pinterest-laravel");
```

Returns: `Board`

### Create board
`create( array $data );`

```php
Pinterest::boards()->create(array(
    "name"          => "Test board from API",
    "description"   => "Test Board From API Test"
));
```

Returns: `Board`

### Edit board
`edit( string $board_id, array $data, string $fields = null );`

```php
Pinterest::boards-edit("waleedahmad/pinterest-laravel", array(
    "name"  => "Test board after edit"
));
```

Returns: `Board`

### Delete board
`delete( string $board_id, array $data );`

```php
Pinterest::boards()->delete("waleedahmad/pinterest-laravel");
```

Returns: `True|PinterestException`

## Pins

The methods below are available through `Pinterest::pins`.

### Get pin
`get( string $pin_id, array $data );`

```php
Pinterest::pins()->get("181692166190246650");
```

Returns: `Pin`

### Get pins from board
`fromBoard( string $board_id, array $data );`

```php
Pinterest::pins()->fromBoard("waleedahmad/pinterest-laravel");
```

Returns: `Collection<Pin>`

### Create pin
`create( array $data );`

Creating a pin with an image hosted somewhere else:

```php
Pinterest::pins()->create(array(
    "note"          => "Test board from API",
    "image_url"     => "https://download.unsplash.com/photo-1438216983993-cdcd7dea84ce",
    "board"         => "waleedahmad/pinterest-laravel"
));
```

Creating a pin with an image located on the server:

```php
Pinterest::pins()->create(array(
    "note"          => "Test board from API",
    "image"         => "/path/to/image.png",
    "board"         => "waleedahmad/pinterest-laravel"
));
```

Creating a pin with a base64 encoded image:

```php
Pinterest::pins()->create(array(
    "note"          => "Test board from API",
    "image_base64"  => "[base64 encoded image]",
    "board"         => "waleedahmad/pinterest-laravel"
));
```


Returns: `Pin`

### Edit pin

`edit( string $pin_id, array $data, string $fields = null );`

```php
Pinterest::pins()->edit("181692166190246650", array(
    "note"  => "Updated name"
));
```

Returns: `Pin`

### Delete pin
`delete( string $pin_id, array $data );`

```php
Pinterest::pins()->delete("181692166190246650");
```

Returns: `True|PinterestException`

## Following

The methods below are available through `Pinterest::following`.

### Following users
`users( array $data );`

```php
Pinterest::following()->users();
```

Returns: `Collection<User>`

### Following boards
`boards( array $data );`

```php
Pinterest::following()->boards();
```

Returns: `Collection<Board>`

### Following interests/categories
`interests( array $data );`

```php
Pinterest::following()->interests();
```

Returns: `Collection<Interest>`

### Follow an user
`followUser( string $username_or_id );`

```php
Pinterest::following()->followUser("waleedahmad");
```

Returns: `True|PinterestException`

### Unfollow an user
`unfollowUser( string $username_or_id );`

```php
Pinterest::following()->unfollowUser("waleedahmad");
```

Returns: `True|PinterestException`

### Follow a board
`followBoard( string $board_id );`

```php
Pinterest::following()->followBoard("503066289565421201");
```

Returns: `True|PinterestException`

### Unfollow a board
`unfollowBoard( string $board_id );`

```php
Pinterest::following()->unfollowBoard("503066289565421201");
```

Returns: `True|PinterestException`

### Follow an interest

> According to the Pinterest documentation this endpoint exists, but for some reason their API is returning an error at the moment.

`followInterest( string $interest );`

```php
Pinterest::following()->followInterest("architecten-911112299766");
```

Returns: `True|PinterestException`

### Unfollow an interest

> According to the Pinterest documentation this endpoint exists, but for some reason their API is returning an error at the moment.

`unfollowInterest( string $interest );`

```php
Pinterest::following()->unfollowInterest("architecten-911112299766");
```

Returns: `True|PinterestException`
