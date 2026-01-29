# Developer Docs

*   Introduction
*   Base Urls
*   Azampay API Flow
*   Authentication
*   APIs
    *   Token Generation
    *   Checkout API
    *   Checkout Pages
    *   Disbursement
    *   Bill Pay API

# Azampay API (v1)

Download OpenAPI specification:[Download]()

# Introduction

AzamPay is specialized in the development of end-to-end online payment management solutions for companies operating in East Africa. Our range of digital solutions and services are carefully designed not only to streamline your payment and collection processes, but to also allow easy integration with your current Accounting or Enterprise Resource Planning (ERP) systems thus leaving you time to focus on your customers. AzamPay offers bespoke solutions that guarantee optimal business performance and efficiency whether you are transacting locally, regionally, or internationally.

We strive to consistently improve our products to better meet the needs of a dynamic East African payments environment. As an AzamPay client, you will be able to leverage your presence across East Africa and extend your services regionally. Remember, we endeavour to follow you throughout your business adventure.

.

# Base Urls

Sandbox

*   **Authenticator Sandbox Base Url: [https://authenticator-sandbox.azampay.co.tz]()**.
*   **Azampay Sandbox Checkout Base Url: [https://sandbox.azampay.co.tz]()**.

# Azampay API Flow

All Azampay APIs follow two step process:

*   Get token against the application authentication credentials.

Following diagram shows the general flow on how to consume the Azampay api.

# Authentication

Azampay offers one form of authentication to ensure secure access to your account:

*   Bearer Auth - an open protocol to allow secure authorization in a simple and standard method from web, mobile and desktop applications.

Bearer Token is the JWT token that you get against your application Name, Client Id and Client Secret. For Sandbox Environment, You can get these application credentials from Sandbox portal. For production environment, you will be provided these keys after you submit your business KYC to AzamPay from Sandbox portal.

# Token Generation

## Generate Token For App

post/AppRegistration/GenerateToken

Generate the access token in order to access Azampay public end points.

##### Request Body schema: application/json

| appName

required

 | 

string

It will be the name of application.







 |
| clientId

required

 | 

string

It will be the client id which generated during application registration.







 |
| clientSecret

required

 | 

string

It will be the secret key which generated during application registration.







 |

### Responses

**200**

Success

**423**

Invalid detail

**500**

Internal Server Error

### Request samples

Content type

application/json

Copy

`{  *   "appName": "string",      *   "clientId": "string",      *   "clientSecret": "string"       }`

### Response samples

Content type

application/json

Copy

Expand all Collapse all

`{  *   "data": {          *   "accessToken": {},              *   "expire": {}                   },      *   "message": "Token generated successfully",      *   "success": true,      *   "statusCode": 200       }`

# Checkout API

## Mno Checkout

post/azampay/mno/checkout

Checkout and make payment to requested provider.

##### Authorizations:

_Bearer Auth_

##### Request Body schema: application/json

| accountNumber

required

 | 

string

This is the account number/MSISDN that consumer will provide. The amount will be deducted from this account.







 |
| additionalProperties | 

object or null

Total serialized size limit: 4 Kilobytes (4096 bytes).







 |
| amount

required

 | 

number

Must contain numeric characters only. Value range: 0 to 5,000,000.







 |
| currency

required

 | 

string

Maximum length: 32 characters.







 |
| externalId

required

 | 

string

Maximum length: 128 characters.







 |
| provider

required

 | 

string (Provider)

Enum: "Airtel" "Tigo" "Halopesa" "Azampesa" "Mpesa"



 |

### Responses

**200**

Success

**400**

Bad Request

**500**

Internal Server Error

### Request samples

Content type

application/json

Copy

Expand all Collapse all

`{  *   "accountNumber": "string",      *   "additionalProperties": {          *   "property1": null,              *   "property2": null                   },      *   "amount": 0,      *   "currency": "string",      *   "externalId": "string",      *   "provider": "Airtel"       }`

### Response samples

Content type

application/json

Copy

`{  *   "transactionId": "string",      *   "message": "string",      *   "success": true       }`

## Bank Checkout

post/azampay/bank/checkout

Checkout and make payment to requested provider.

##### Authorizations:

_Bearer Auth_

##### Request Body schema: application/json

| additionalProperties | 

object or null

Total serialized size limit: 4096 bytes (4 Kilobytes).







 |
| amount

required

 | 

number

Must contain numeric characters only. Value range: 0 to 5,000,000.







 |
| currencyCode

required

 | 

string

Cannot be null or empty.







 |
| merchantAccountNumber

required

 | 

string

Maximum length: 100 characters.







 |
| merchantMobileNumber

required

 | 

string

Maximum length: 100 characters.







 |
| merchantName | 

string or null

Maximum length: 100 characters.







 |
| otp

required

 | 

string

Marked as Sensitive Data; ensure secure handling.







 |
| provider

required

 | 

string (BankProvider)

Enum: "CRDB" "NMB"



 |
| referenceId | 

string or null

Maximum length: 128 characters.







 |

### Responses

**200**

Success

**400**

Bad Request

**500**

Internal Server Error

### Request samples

Content type

application/json

Copy

Expand all Collapse all

`{  *   "additionalProperties": {          *   "property1": null,              *   "property2": null                   },      *   "amount": 0,      *   "currencyCode": "string",      *   "merchantAccountNumber": "string",      *   "merchantMobileNumber": "string",      *   "merchantName": "string",      *   "otp": "string",      *   "provider": "CRDB",      *   "referenceId": "string"       }`

### Response samples

Content type

application/json

Copy

`{  *   "transactionId": "string",      *   "message": "string",      *   "success": true       }`

## Generate CRDB OTP

**How to get CRDB OTP to activate your bank account**

Dial \*150\*03# and Enter your SIM Banking PIN

Press 7 other services

Press 5 for azampay the select any of the below

Link Azampay Account > to generate OTP

Unlink Azampay Account > unlink linked account

Disconnect > disable linking

## Generate NMB OTP

**How to get NMB OTP to activate your bank account**

Dial \*150\*66#

Press 8 More

Press 5 Register Sarafu

Press 1 Select Account No.

## Callback

post/api/v1/Checkout/Callback

This endpoint must be available in the your application all the time. This application will send transaction completion status to merchant application upon confirmation by user.

For Sandbox environment, the URL for this callback can be provided upon registering the app

For Production, after approval of submitted KYC

 You will be asked to provide the production URL for the callback by the Payment Gateway Customer Care team to integrate.

Callback endpoint must follow below provided schema

##### Request Body schema: application/json

| additionalProperties | 

object or null

This is additional JSON data that calling application can provide. This is optional.







 |
| amount

required

 | 

string

This is amount that will be charged from the given account.







 |
| fspReferenceId | 

string

It is the reference ID from partner FSP (Financial Service Provider)







 |
| message

required

 | 

string

This is transaction description message







 |
| msisdn

required

 | 

string

This is the account number/MSISDN that consumer will provide. The amount will be deducted from this account.







 |
| provider

required

 | 

string

This is the provider that consumer will provide. The amount will be deducted from this account.







 |
| statusCode

required

 | 

number

This is the status code of the transaction.







 |
| success

required

 | 

bool

This is the status of the transaction.







 |
| transactionId

required

 | 

string

This is the transaction ID that will be generated by AzamPay.







 |

### Responses

**200**

Success

**400**

Bad Request

**500**

Internal Server Error

### Request samples

Content type

application/json

Copy

Expand all Collapse all

`{  *   "additionalProperties": {          *   "property1": null,              *   "property2": null                   },      *   "amount": "string",      *   "fspReferenceId": "string",      *   "message": "string",      *   "msisdn": "string",      *   "provider": "string",      *   "statusCode": 0,      *   "success": true,      *   "transactionId": "string"       }`

### Response samples

Content type

application/json

Copy

`{  *   "message": "string",      *   "success": true       }`

# Checkout Pages

## Initiate Checkout Session

post/azampay/checkout/json

Initiate a checkout session for web and mobile.

##### Authorizations:

_Bearer Auth_

##### Request Body schema: application/json

| amount

required

 | 

number

Must contain numeric characters only. Value range: 0 to 5,000,000.







 |
| currency

required

 | 

string

Maximum length: 32 characters.







 |
| externalId

required

 | 

string

Maximum length: 128 characters.







 |
| redirectFailURL

required

 | 

string

URL to redirect to if payment fails.







 |
| redirectSuccessURL

required

 | 

string

URL to redirect to if payment succeeds.







 |

### Responses

**200**

Success

**400**

Bad Request

**500**

Internal Server Error

### Request samples

Content type

application/json

Copy

Expand all Collapse all

`{  *   "amount": 0,      *   "currency": "string",      *   "externalId": "string",      *   "redirectFailURL": "string",      *   "redirectSuccessURL": "string"       }`

### Response samples

Content type

application/json

Copy

`{  *   "message": "string",      *   "pgUrl": "string",      *   "success": true,      *   "transactionId": "string"       }`

# Disbursement

## Initiate Disbursement

post/azampay/disbursement

Initiate a disbursement.

##### Authorizations:

_Bearer Auth_

##### Request Body schema: application/json

| amount

required

 | 

number

Must contain numeric characters only. Value range: 0 to 5,000,000.







 |
| currency

required

 | 

string

Maximum length: 32 characters.







 |
| externalId

required

 | 

string

Maximum length: 128 characters.







 |
| receiverAccountNumber

required

 | 

string

This is the account number/MSISDN that will receive the disbursement.







 |
| receiverName

required

 | 

string

Name of the receiver.







 |
| remarks | 

string or null

Maximum length: 256 characters.







 |

### Responses

**200**

Success

**400**

Bad Request

**500**

Internal Server Error

### Request samples

Content type

application/json

Copy

Expand all Collapse all

`{  *   "amount": 0,      *   "currency": "string",      *   "externalId": "string",      *   "receiverAccountNumber": "string",      *   "receiverName": "string",      *   "remarks": "string"       }`

### Response samples

Content type

application/json

Copy

`{  *   "message": "string",      *   "success": true,      *   "transactionId": "string"       }`

# Bill Pay API

## Get Bill Categories

get/azampay/bill-pay/categories

Get all bill categories.

##### Authorizations:

_Bearer Auth_

### Responses

**200**

Success

**400**

Bad Request

**500**

Internal Server Error

### Response samples

Content type

application/json

Copy

`{  *   "data": [          *   {              *   "id": "string",                  *   "name": "string"                   }          ],      *   "message": "string",      *   "success": true       }`

## Get Bills By Category

get/azampay/bill-pay/bills

Get all bills by category.

##### Authorizations:

_Bearer Auth_

##### Query Parameters

| categoryId

required

 | 

string

ID of the bill category.







 |

### Responses

**200**

Success

**400**

Bad Request

**500**

Internal Server Error

### Response samples

Content type

application/json

Copy

`{  *   "data": [          *   {              *   "amount": 0,                  *   "billId": "string",                  *   "billName": "string",                  *   "category": "string",                  *   "logo": "string",                  *   "maxAmount": 0,                  *   "minAmount": 0,                  *   "paymentType": "string"                   }          ],      *   "message": "string",      *   "success": true       }`

## Pay Bill

post/azampay/bill-pay/pay

Pay a bill.

##### Authorizations:

_Bearer Auth_

##### Request Body schema: application/json

| amount

required

 | 

number

Must contain numeric characters only. Value range: 0 to 5,000,000.







 |
| billId

required

 | 

string

ID of the bill to pay.







 |
| customerUid

required

 | 

string

Unique identifier for the customer.







 |
| provider

required

 | 

string (Provider)

Enum: "Airtel" "Tigo" "Halopesa" "Azampesa" "Mpesa"



 |

### Responses

**200**

Success

**400**

Bad Request

**500**

Internal Server Error

### Request samples

Content type

application/json

Copy

Expand all Collapse all

`{  *   "amount": 0,      *   "billId": "string",      *   "customerUid": "string",      *   "provider": "Airtel"       }`

### Response samples

Content type

application/json

Copy

`{  *   "message": "string",      *   "success": true,      *   "transactionId": "string"       }`



