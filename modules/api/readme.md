# perfex-rest-api
perfex crm REST API module version : `0.1.0`

### [api doc help](https://apidocjs.com/)
##### [install](https://apidocjs.com/)

##### [template](https://apidocjs.com/#template)

##### [configuration (apidoc.json)](https://apidocjs.com/#configuration)

##### [versioning](https://apidocjs.com/#example-versioning)

##### [full-example](https://apidocjs.com/#example-full)

### how to add? 

```
npm install apidoc -g
```

### generate command : 
```
apidoc -i ../modules/api -o doc/
```

#### Sample code for comments

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
 */
```
=======================================================================================

## Parameters list for Contact

	First Name (string|required)
	Last Name (string|required)
	Email (string|required|email)
	Password (string|required)
	
	Position (string)
	Phone (string)
	
	Primary Contact (checked / unchecked) - default checked (if primary then don't show While in Edit Mode)
	Do not send welcome email (checked / unchecked) (don't show while edit data)
	Send SET password email (checked / unchecked)
	
	Invoices (on / off)
	Estimates (on / off)
	Contracts (on / off)
	Proposals (on / off)
	Support (on / off)
	Projects (on / off)
	
	Invoice (on / off)
	Estimate (on / off)
	Credit Note (on / off)
	Project (on / off)
	Tickets (on / off)
	Task (on / off)
	Contract (on / off)
## Parameters list for Inovice

```
* Customer (dropdown)
* Invoice Number (number) also fetch inovice pattern in settings.
* Invoice Date
* Currency (disabled, already selected)

Due Date
Tags
Allowed payment modes for this invoice
Sale Agent (dropdown)
Recurring Invoice? (dropdown)
Discount Type (dropdown)
Admin Note (text area)
Client Note (text area)
Terms & Conditions (text area)
```



## 1) Contact API

###### Add New Contact

``` json response```

------

###### Delete a Contact

``` json response```

------

###### Request Contact information

``` json response```

------

###### Search Contact Information

``` json response```

------

###### Update a Contact

``` json response```

=======================================================================================

## 2) Invoice API

###### Add new Invoice

``` json response```

------

###### Delete Invoice

``` json response```

------

###### Request Invoice Information

``` json response```

------

###### Search Invoice Information

``` json response```

------

###### Update an Invoice

``` json response```
