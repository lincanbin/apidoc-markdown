# apidoc-markdown
Generate API documentation in markdown format from API annotations in your source code.

## Preface

All examples in this document use the **Javadoc-Style** (can be used in C#, Go, Dart, Java, JavaScript, PHP, TypeScript and all other Javadoc capable languages):

```
/**
 * This is a comment.
 */
```

## Install

```shell
composer global require lincanbin/apidoc-markdown:~1.0
```

## Run

```
apidoc-markdown -i myapp/ -o apidoc/
```

or

```
apidoc -i myapp/ -o apidoc/
```

Creates an apiDoc of all files within dir `myapp/`, and put all output to dir `apidoc/`.

Without any parameter, apiDoc generate a documentation from all `.cs` `.dart` `.erl` `.go` `.java` `.js` `.php` `.py` `.rb` `.ts` files in current dir (incl. subdirs) and writes the output to `./apidoc/`.

## Command Line Interface

Show command line parameters:

```
apidoc-markdown -h
```

Important parameters:

| Parameter          | Description                                                  |
| ------------------ | ------------------------------------------------------------ |
| -f, --file-filters | RegEx-Filter to select files that should be parsed (many -f can be used). Default `.cs` `.dart` `.erl` `.go` `.java` `.js` `.php` `.py` `.rb` `.ts`.  Example (parse only .js and .ts files): `apidoc -f ".*\\.js$" -f ".*\\.ts$"` |
| -i, --input        | Input / source dirname. Location of your project files.  Example: `apidoc -i myapp/` |
| -o, --output       | Output dirname. Location where to put to generated documentation.  Example: `apidoc -o apidoc/` |
| -t, --template     | Use template for output files. You can create and use your own template.  Example: `apidoc -t mytemplate/` |

## Configuration (apidoc.json)

The optional `apidoc.json` in your projects root dir includes common information about your project like title, short description, version and configuration options like [header / footer](#headerfooter)settings or template specific options.

[apidoc.json](source/example_full/apidoc.json)

```
{
  "name": "example",
  "version": "0.1.0",
  "description": "apiDoc basic example",
  "title": "Custom apiDoc browser title",
  "url" : "https://api.github.com/v1"
}
```

If you use a `package.json` (e.g. in a node.js project), all `apidoc.json` settings can be done in `package.json` too, just add them under the `"apidoc": { }` parameter.

package.json

```
{
  "name": "example",
  "version": "0.1.0",
  "description": "apiDoc basic example",
  "apidoc": {
    "title": "Custom apiDoc browser title",
    "url" : "https://api.github.com/v1"
  }
}
```

### Settings for apidoc.json

| Name        | Description                                                  |
| ----------- | ------------------------------------------------------------ |
| name        | Name of your project. If no `apidoc.json` with the field exists, then apiDoc try to determine the the value from `package.json`. |
| version     | Version of your project. If no `apidoc.json` with the field exists, then apiDoc try to determine the the value from `package.json`. |
| description | Introduction of your project. If no `apidoc.json` with the field exists, then apiDoc try to determine the the value from `package.json`. |
| title       | Browser title text.                                          |
| url         | Prefix for api path (endpoints), e.g. `https://api.github.com/v1` |
| sampleUrl   | If set, a form to test an api method (send a request) will be visible. See [@apiSampleRequest](#param-api-sample-request) for more details. |
| header      |                                                              |
| title       | Navigation text for the included header.md file. (watch [Header / Footer](#headerfooter)) |
| filename    | Filename (markdown-file) for the included header.md file.    |
| footer      |                                                              |
| title       | Navigation text for the included footer.md file.             |
| filename    | Filename (markdown-file) for the included footer.md file.    |
| order       | A list of api-names / group-names for ordering the output. Not defined names are automatically displayed last. `"order": [   "Error",   "Define",   "PostTitleAndError",   "PostError" ]` |

### Template specific settings

The following settings are specific for the default template of apiDoc.

| Name            | Type    | Description                                                  |
| --------------- | ------- | ------------------------------------------------------------ |
| template        |         |                                                              |
| forceLanguage   | String  | Disable browser language auto-detection and set a specific locale. Example: `de`, `en`. View available locales [here](https://github.com/apidoc/apidoc/tree/master/template/locales). |
| withCompare     | Boolean | Enable comparison with older api versions. Default: `true`   |
| withGenerator   | Boolean | Output the generator information at the footer. Default: `true` |
| jQueryAjaxSetup | Object  | Set [default values](http://api.jquery.com/jquery.ajaxsetup/) for Ajax requests. |

## Header / Footer

In your projects `apidoc.json` you can add a header and footer.

The title will be visible in the navigation. The filename should be a markdown textfile.

[apidoc.json](source/example_full/apidoc.json)

```
{
  "header": {
    "title": "My own header title",
    "filename": "header.md"
  },
  "footer": {
    "title": "My own footer title",
    "filename": "footer.md"
  }
}
```

## Basic

In this basic example we have a small project file and an apidoc.json.

[View example output](example_basic/)

[apidoc.json](source/example_basic/apidoc.json)

```
{
  "name": "example",
  "version": "0.1.0",
  "description": "A basic apiDoc example"
}
```

From `apidoc.json` apiDoc get the name, version and description of your project.
The file is `optional` (it depend on your template if the data is required).

[example.js](source/example_basic/example.js)

```
/**
 * @api {get} /user/:id Request User information
 * @apiName GetUser
 * @apiGroup User
 *
 * @apiParam {Number} id Users unique ID.
 *
 * @apiSuccess {String} firstname Firstname of the User.
 * @apiSuccess {String} lastname  Lastname of the User.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "firstname": "John",
 *       "lastname": "Doe"
 *     }
 *
 * @apiError UserNotFound The id of the User was not found.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "UserNotFound"
 *     }
 */
```

A documentation block starts with `/**` and end with `*/`.

This example describes a `GET` Method to request the User Information by the user's `id`.

`@api {get} /user/:id Request User information` is mandatory, without `@api`apiDoc ignores a documentation block.

`@apiName` must be a unique name and should always be used.
Format: *method* + *path* (e.g. Get + User)

`@apiGroup` should always be used, and is used to group related APIs together.

All other fields are optional, look at their description under [apiDoc-Params](#params).

## Inherit

Using inherit, you can define reusable snippets of your documentation.

[View example output](example_inherit/)

[apidoc.json](source/example_inherit/apidoc.json)

```
{
  "name": "example-inherit",
  "version": "0.1.0",
  "description": "apiDoc inherit example"
}
```

[example.js](source/example_inherit/example.js)

```
/**
 * @apiDefine UserNotFoundError
 *
 * @apiError UserNotFound The id of the User was not found.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "UserNotFound"
 *     }
 */

/**
 * @api {get} /user/:id Request User information
 * @apiName GetUser
 * @apiGroup User
 *
 * @apiParam {Number} id Users unique ID.
 *
 * @apiSuccess {String} firstname Firstname of the User.
 * @apiSuccess {String} lastname  Lastname of the User.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "firstname": "John",
 *       "lastname": "Doe"
 *     }
 *
 * @apiUse UserNotFoundError
 */

/**
 * @api {put} /user/ Modify User information
 * @apiName PutUser
 * @apiGroup User
 *
 * @apiParam {Number} id          Users unique ID.
 * @apiParam {String} [firstname] Firstname of the User.
 * @apiParam {String} [lastname]  Lastname of the User.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *
 * @apiUse UserNotFoundError
 */
```

In this example, a block named `UserNotFoundError` is defined with `@apiDefine`.
That block could be used many times with `@apiUse UserNotFoundError`.

In the generated output, both methods `GET` and `PUT` will have the complete `UserNotFoundError` documentation.

To define an inherit block, use `apiDefine`.
to reference a block, use `apiUse`. `apiGroup` and `apiPermission` are use commands to, but in their context the not inherit parameters, only title and description (in combination with apiVersion).

**Inheritation only works with 1 parent**, more levels would make the inline code unreadable and changes really complex.

## Versioning

A useful feature provided by apiDoc is the ability to maintain the documentation for all previous versions and the latest version of the API. This makes it possible to compare a methods version with its predecessor. Frontend Developer can thus simply see what have changed and update their code accordingly.

[View example output](example_versioning/)

In the example, click top right on select box (the main version) and select `Compare all with predecessor`.

- The main navigation mark all changed methods with a green bar.
- Each method show the actual difference compare to its predecessor.
- Green marks contents that were added (in this case title text changed and field `registered` was added).
- Red marks contents that were removed.

You can change the main version (top right) to a previous version and compare older methods with their predecessor.

[apidoc.json](source/example_inherit/apidoc.json)

```
{
  "name": "example-versioning",
  "version": "0.2.0",
  "description": "apiDoc versioning example"
}
```

In order to avoid code bloat when API documentation changes over time, it is recommended to use a separate history file named `_apidoc.js`. Before you change your documentation block, copy the old documentation to to this file, apiDoc will include the historical information automatically.

[_apidoc.js](source/example_versioning/_apidoc.js)

```
/**
 * @api {get} /user/:id Get User information
 * @apiVersion 0.1.0
 * @apiName GetUser
 * @apiGroup User
 *
 * @apiParam {Number} id Users unique ID.
 *
 * @apiSuccess {String} firstname Firstname of the User.
 * @apiSuccess {String} lastname  Lastname of the User.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "firstname": "John",
 *       "lastname": "Doe"
 *     }
 *
 * @apiError UserNotFound The id of the User was not found.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "UserNotFound"
 *     }
 */
```

[example.js](source/example_versioning/example.js) (your current project file)

```
/**
 * @api {get} /user/:id Get User information and Date of Registration.
 * @apiVersion 0.2.0
 * @apiName GetUser
 * @apiGroup User
 *
 * @apiParam {Number} id Users unique ID.
 *
 * @apiSuccess {String} firstname  Firstname of the User.
 * @apiSuccess {String} lastname   Lastname of the User.
 * @apiSuccess {Date}   registered Date of Registration.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "firstname": "John",
 *       "lastname": "Doe"
 *     }
 *
 * @apiError UserNotFound The id of the User was not found.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "UserNotFound"
 *     }
 */
```

Important is to set the version with `@apiVersion` on every documentation block.

The version can be used on every block, also on inherit blocks. You don't have to change the version on an inherit block, the parser check automatically for the nearest predecessor.

## Full example

This is a complex example with `inherit`, `versioning` file and history file `_apidoc.js`, explanation is within code and generated documentation.

[View example output](example/)

Files:

- [_apidoc.js](source/example_full/_apidoc.js)
- [example.js](source/example_full/example.js)
- [apidoc.json](source/example_full/apidoc.json)

# apiDoc-Params

Structure parameter like:

- `@apiDefine`

is used to define a reusable documentation block. This block can be included in normal api documentation blocks. Using `@apiDefine` allows you to better organize complex documentation and avoid duplicating recurrent blocks.

A defined block can have all params (like `@apiParam`), **except other defined blocks**.

## @api

```
@api {method} path [title]
```

**Required!**

Without that indicator, apiDoc parser ignore the documentation block.

The only exception are documentation blocks defined by `@apiDefine`, they not required `@api`.

Usage: `@api {get} /user/:id Users unique ID.`

| Name          | Description                                                  |
| ------------- | ------------------------------------------------------------ |
| method        | Request method name: `DELETE`, `GET`, `POST`, `PUT`, ... More info [Wikipedia HTTP-Request_methods](http://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods) |
| path          | Request Path.                                                |
| titleoptional | A short title. (used for navigation and article header)      |

Example:

```
/**
 * @api {get} /user/:id
 */
```

## @apiDefine

```
@apiDefine name [title]
                     [description]
```

Defines a documentation block to be embedded within `@api` blocks or in an api function like `@apiPermission`.

`@apiDefine` can only be used once per block

By using `@apiUse` a defined block will be imported, or with the name the title and description will be used.

Usage: `@apiDefine MyError`

| Name                | Description                                                  |
| ------------------- | ------------------------------------------------------------ |
| name                | Unique name for the block / value. Same name with different `@apiVersion` can be defined. |
| titleoptional       | A short title. Only used for named functions like `@apiPermission` or `@apiParam (name)`. |
| descriptionoptional | Detailed Description start at the next line, multiple lines can be used. Only used for named functions like `@apiPermission`. |

Example:

```
/**
 * @apiDefine MyError
 * @apiError UserNotFound The <code>id</code> of the User was not found.
 */

/**
 * @api {get} /user/:id
 * @apiUse MyError
 */
```

```
/**
 * @apiDefine admin User access only
 * This optional description belong to to the group admin.
 */

/**
 * @api {get} /user/:id
 * @apiPermission admin
 */
```

For more details, see [inherit example](#example-inherit).

## @apiDeprecated

```
@apiDeprecated [text]
```

Mark an API Method as deprecated

Usage: `@apiDeprecated use now (#Group:Name).`

| Name | Description     |
| ---- | --------------- |
| text | Multiline text. |

Example:

```
/**
 * @apiDeprecated
 */

/**
 * @apiDeprecated use now (#Group:Name).
 *
 * Example: to set a link to the GetDetails method of your group User
 * write (#User:GetDetails)
 */
```

## @apiDescription

```
@apiDescription text
```

Detailed description of the API Method.

Usage: `@apiDescription This is the Description.`

| Name | Description                 |
| ---- | --------------------------- |
| text | Multiline description text. |

Example:

```
/**
 * @apiDescription This is the Description.
 * It is multiline capable.
 *
 * Last line of Description.
 */
```

## @apiError

```
@apiError [(group)] [{type}] field [description]
```

Error return Parameter.

Usage: `@apiError UserNotFound`

| Name                | Description                                                  |
| ------------------- | ------------------------------------------------------------ |
| (group)optional     | All parameters will be grouped by this name. Without a group, the default `Error 4xx` is set. You can set a title and description with [@apiDefine](#param-api-define). |
| {type}optional      | Return type, e.g. `{Boolean}`, `{Number}`, `{String}`,`{Object}`, `{String[]}` (array of strings), ... |
| field               | Return Identifier (returned error code).                     |
| descriptionoptional | Description of the field.                                    |

Example:

```
/**
 * @api {get} /user/:id
 * @apiError UserNotFound The <code>id</code> of the User was not found.
 */
```

## @apiErrorExample

```
@apiErrorExample [{type}] [title]
                 example
```

Example of an error return message, output as a pre-formatted code.

Usage: `@apiErrorExample {json} Error-Response:                 This is an example.`

| Name          | Description                           |
| ------------- | ------------------------------------- |
| typeoptional  | Response format.                      |
| titleoptional | Short title for the example.          |
| example       | Detailed example, multilines capable. |

Example:

```
/**
 * @api {get} /user/:id
 * @apiErrorExample {json} Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "UserNotFound"
 *     }
 */
```

## @apiExample

```
@apiExample [{type}] title
            example
```

Example for usage of an API method. Output as a pre-formatted code.

Use it for a complete example at the beginning of the description of an endpoint.

Usage: `@apiExample {js} Example usage:            This is an example.`

| Name         | Description                           |
| ------------ | ------------------------------------- |
| typeoptional | Code language.                        |
| title        | Short title for the example.          |
| example      | Detailed example, multilines capable. |

Example:

```
/**
 * @api {get} /user/:id
 * @apiExample {curl} Example usage:
 *     curl -i http://localhost/user/4711
 */
```

## @apiGroup

```
@apiGroup name
```

**Should always be used.**

Defines to which group the method documentation block belongs. Groups will be used for the Main-Navigation in the generated output. Structure definition not need `@apiGroup`.

Usage: `@apiGroup User`

| Name | Description                                       |
| ---- | ------------------------------------------------- |
| name | Name of the group. Also used as navigation title. |

Example:

```
/**
 * @api {get} /user/:id
 * @apiGroup User
 */
```

## @apiHeader

```
@apiHeader [(group)] [{type}] [field=defaultValue] [description]
```

Describe a parameter passed to you API-Header e.g. for Authorization.

Similar operation as [@apiParam](#param-api-param), only the output is above the parameters.

Usage: `@apiHeader (MyHeaderGroup) {String} authorization Authorization value.`

| Name                  | Description                                                  |
| --------------------- | ------------------------------------------------------------ |
| (group)optional       | All parameters will be grouped by this name. Without a group, the default `Parameter` is set. You can set a title and description with [@apiDefine](#param-api-define). |
| {type}optional        | Parameter type, e.g. `{Boolean}`, `{Number}`, `{String}`,`{Object}`, `{String[]}` (array of strings), ... |
| field                 | Variablename.                                                |
| [field]               | Fieldname with brackets define the Variable as optional.     |
| =defaultValueoptional | The parameters default value.                                |
| descriptionoptional   | Description of the field.                                    |

Examples:

```
/**
 * @api {get} /user/:id
 * @apiHeader {String} access-key Users unique access-key.
 */
```

## @apiHeaderExample

```
@apiHeaderExample [{type}] [title]
                   example
```

Parameter request example.

Usage: `@apiHeaderExample {json} Request-Example:                 { "content": "This is an example content" }`

| Name          | Description                           |
| ------------- | ------------------------------------- |
| typeoptional  | Request format.                       |
| titleoptional | Short title for the example.          |
| example       | Detailed example, multilines capable. |

Example:

```
/**
 * @api {get} /user/:id
 * @apiHeaderExample {json} Header-Example:
 *     {
 *       "Accept-Encoding": "Accept-Encoding: gzip, deflate"
 *     }
 */
```

## @apiIgnore

```
@apiIgnore [hint]
```

**Place it on top of a block.**

A block with `@apiIgnore` will not be parsed. It is usefull, if you leave outdated or not finished Methods in your source code and you don't want to publish it into the documentation.

Usage: `@apiIgnore Not finished Method`

| Name         | Description                                         |
| ------------ | --------------------------------------------------- |
| hintoptional | Short information why this block should be ignored. |

Example:

```
/**
 * @apiIgnore Not finished Method
 * @api {get} /user/:id
 */
```

## @apiName

```
@apiName name
```

**Should always be used.**

Defines the name of the method documentation block. Names will be used for the Sub-Navigation in the generated output. Structure definition not need `@apiName`.

Usage: `@apiName GetUser`

| Name | Description                                                  |
| ---- | ------------------------------------------------------------ |
| name | Unique name of the method. Same name with different `@apiVersion` can be defined. Format: *method* + *path* (e.g. Get + User), only a proposal, you can name as you want. Also used as navigation title. |

Example:

```
/**
 * @api {get} /user/:id
 * @apiName GetUser
 */
```

## @apiParam

```
@apiParam [(group)] [{type}] [field=defaultValue] [description]
```

Describe a parameter passed to you API-Method.

Usage: `@apiParam (MyGroup) {Number} id Users unique ID.`

| Name                         | Description                                                  |
| ---------------------------- | ------------------------------------------------------------ |
| (group)optional              | All parameters will be grouped by this name. Without a group, the default `Parameter` is set. You can set a title and description with [@apiDefine](#param-api-define). |
| {type}optional               | Parameter type, e.g. `{Boolean}`, `{Number}`, `{String}`, `{Object}`, `{String[]}` (array of strings), ... |
| {type{size}}optional         | Information about the size of the variable. `{string{..5}}` a string that has max 5 chars. `{string{2..5}}` a string that has min. 2 chars and max 5 chars. `{number{100-999}}` a number between 100 and 999. |
| {type=allowedValues}optional | Information about allowed values of the variable. `{string="small"}` a string that can only contain the word "small" (a constant). `{string="small","huge"}` a string that can contain the words "small" or "huge". `{number=1,2,3,99}` a number with allowed values of 1, 2, 3 and 99.  Can be combined with size: `{string {..5}="small","huge"}` a string that has max 5 chars and only contain the words "small" or "huge". |
| field                        | Variablename.                                                |
| [field]                      | Fieldname with brackets define the Variable as optional.     |
| =defaultValueoptional        | The parameters default value.                                |
| descriptionoptional          | Description of the field.                                    |

Examples:

```
/**
 * @api {get} /user/:id
 * @apiParam {Number} id Users unique ID.
 */

/**
 * @api {post} /user/
 * @apiParam {String} [firstname]  Optional Firstname of the User.
 * @apiParam {String} lastname     Mandatory Lastname.
 * @apiParam {String} country="DE" Mandatory with default value "DE".
 * @apiParam {Number} [age=18]     Optional Age with default 18.
 *
 * @apiParam (Login) {String} pass Only logged in users can post this.
 *                                 In generated documentation a separate
 *                                 "Login" Block will be generated.
 */
```

## @apiParamExample

```
@apiParamExample [{type}] [title]
                   example
```

Parameter request example.

Usage: `@apiParamExample {json} Request-Example:                 { "content": "This is an example content" }`

| Name          | Description                           |
| ------------- | ------------------------------------- |
| typeoptional  | Request format.                       |
| titleoptional | Short title for the example.          |
| example       | Detailed example, multilines capable. |

Example:

```
/**
 * @api {get} /user/:id
 * @apiParamExample {json} Request-Example:
 *     {
 *       "id": 4711
 *     }
 */
```

## @apiPermission

```
@apiPermission name
```

Outputs the permission name. If the name is defined with `@apiDefine` the generated documentation include the additional title and description.

Usage: `@apiPermission admin`

| Name | Description                    |
| ---- | ------------------------------ |
| name | Unique name of the permission. |

Example:

```
/**
 * @api {get} /user/:id
 * @apiPermission none
 */
```

## @apiPrivate

```
@apiPrivate
```

Defines an API as being private to allow the creation of two API specification documents: one that excludes the private APIs and one that includes them.

Usage: `@apiPrivate`

Command line usage to exclude/include private APIs: `--private false|true`

Example:

```
/**
 * @api {get} /user/:id
 * @apiPrivate
 */
```

## @apiSampleRequest

```
@apiSampleRequest url
```

Use this parameter in conjunction with the apidoc.json configuration parameter [sampleUrl](#configuration-settings-sample-url).

If `sampleUrl` is set, all methods will have the api test form (the endpoint from [@api](#param-api) will be appended).
Without sampleUrl only methods with `@apiSampleRequest` will have a form.

if `@apiSampleRequest url` is set in a method block, this url will be used for the request (it overrides sampleUrl when it starts with http).

If `sampleUrl` is set and you don't want a method with a test form, then add `@apiSampleRequest off` to the documentation block.

Usage: `@apiSampleRequest http://test.github.com`

| Name | Description                                                  |
| ---- | ------------------------------------------------------------ |
| url  | Url to your test api server.  Overwrite the configuration parameter sampleUrl and append [@api](#param-api) url: `@apiSampleRequest http://www.example.com`  Prefix the [@api](#param-api) url: `@apiSampleRequest /my_test_path`  Disable api test if configuration parameter sampleUrl is set: `@apiSampleRequest off` |

Examples:

This will send the api request to **http://api.github.com/user/:id**

```
Configuration parameter sampleUrl: "http://api.github.com"
/**
 * @api {get} /user/:id
 */
```

This will send the api request to **http://test.github.com/some_path/user/:id**
It overwrites sampleUrl.

```
Configuration parameter sampleUrl: "http://api.github.com"
/**
 * @api {get} /user/:id
 * @apiSampleRequest http://test.github.com/some_path/
 */
```

This will send the api request to **http://api.github.com/test/user/:id**
It extends sampleUrl.

```
Configuration parameter sampleUrl: "http://api.github.com"
/**
 * @api {get} /user/:id
 * @apiSampleRequest /test
 */
```

This will disable the api request for this api-method.

```
Configuration parameter sampleUrl: "http://api.github.com"
/**
 * @api {get} /user/:id
 * @apiSampleRequest off
 */
```

This will send the api request to **http://api.github.com/some_path/user/:id**
It activates the request for this method only, because sampleUrl is not set.

```
Configuration parameter sampleUrl is not set
/**
 * @api {get} /user/:id
 * @apiSampleRequest http://api.github.com/some_path/
 */
```

## @apiSuccess

```
@apiSuccess [(group)] [{type}] field [description]
```

Success return Parameter.

Usage: `@apiSuccess {String} firstname Firstname of the User.`

| Name                | Description                                                  |
| ------------------- | ------------------------------------------------------------ |
| (group)optional     | All parameters will be grouped by this name. Without a group, the default `Success 200` is set. You can set a title and description with [@apiDefine](#param-api-define). |
| {type}optional      | Return type, e.g. `{Boolean}`, `{Number}`, `{String}`,`{Object}`, `{String[]}` (array of strings), ... |
| field               | Return Identifier (returned success code).                   |
| descriptionoptional | Description of the field.                                    |

Example:

```
/**
 * @api {get} /user/:id
 * @apiSuccess {String} firstname Firstname of the User.
 * @apiSuccess {String} lastname  Lastname of the User.
 */
```

Example with `(group)`, more group-examples at [@apiSuccessTitle](#param-api-success-title):

```
/**
 * @api {get} /user/:id
 * @apiSuccess (200) {String} firstname Firstname of the User.
 * @apiSuccess (200) {String} lastname  Lastname of the User.
 */
```

Example with Object:

```
/**
 * @api {get} /user/:id
 * @apiSuccess {Boolean} active        Specify if the account is active.
 * @apiSuccess {Object}  profile       User profile information.
 * @apiSuccess {Number}  profile.age   Users age.
 * @apiSuccess {String}  profile.image Avatar-Image.
 */
```

Example with Array:

```
/**
 * @api {get} /users
 * @apiSuccess {Object[]} profiles       List of user profiles.
 * @apiSuccess {Number}   profiles.age   Users age.
 * @apiSuccess {String}   profiles.image Avatar-Image.
 */
```

## @apiSuccessExample

```
@apiSuccessExample [{type}] [title]
                   example
```

Example of a success return message, output as a pre-formatted code.

Usage: `@apiSuccessExample {json} Success-Response:                   { "content": "This is an example content" }`

| Name          | Description                           |
| ------------- | ------------------------------------- |
| typeoptional  | Response format.                      |
| titleoptional | Short title for the example.          |
| example       | Detailed example, multilines capable. |

Example:

```
/**
 * @api {get} /user/:id
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "firstname": "John",
 *       "lastname": "Doe"
 *     }
 */
```

## @apiUse

```
@apiUse name
```

Include a with `@apiDefine` defined block. If used with `@apiVersion` the same or nearest predecessor will be included.

Usage: `@apiUse MySuccess`

| Name | Description                |
| ---- | -------------------------- |
| name | Name of the defined block. |

Example:

```
/**
 * @apiDefine MySuccess
 * @apiSuccess {string} firstname The users firstname.
 * @apiSuccess {number} age The users age.
 */

/**
 * @api {get} /user/:id
 * @apiUse MySuccess
 */
```

## @apiVersion

```
@apiVersion version
```

Set the version of an documentation block. Version can also be used in `@apiDefine`.

Blocks with same group and name, but different versions can be compared in the generated output, so you or a frontend developer can retrace what changes in the API since the last version.

Usage: `@apiVersion 1.6.2`

| Name    | Description                                                  |
| ------- | ------------------------------------------------------------ |
| version | Simple versioning supported (major.minor.patch). More info on [Semantic Versioning Specification (SemVer)](http://semver.org/). |

Example:

```
/**
 * @api {get} /user/:id
 * @apiVersion 1.6.2
 */
```

For more watch [versioning example](#example-versioning).