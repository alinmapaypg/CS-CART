# Alinma Pay -- CS-Cart Payment Gateway Integration

## Overview

This repository contains the **CS-Cart Payment Gateway plugin** for
**Alinma Pay**.\
It allows merchants to integrate their **CS-Cart online store** with the
Alinma Pay Payment Gateway to securely accept online payments.

The integration supports transaction initialization, encrypted response
handling, and payment status processing.

------------------------------------------------------------------------

## Document Version

  Version   Description    Date
  --------- -------------- -------------
  3.0.3     Base Version   04-Feb-2026

------------------------------------------------------------------------

# Introduction

**CS-Cart** is an open‑source eCommerce platform used by merchants to
build and manage online stores.

By integrating **Alinma Pay Payment Gateway**, your CS‑Cart store can
accept online payments securely using supported payment methods.

Before integrating the payment gateway, ensure that you have a
**registered merchant account**.

------------------------------------------------------------------------

# Requirements

## Compatibility

  Component   Version
  ----------- ---------
  PHP         8.2.12
  CS‑Cart     4.18.3

------------------------------------------------------------------------

# Prerequisites

Before starting the integration:

1.  Create a **Merchant Dashboard account**.
2.  Obtain the required **merchant credentials**.

### Merchant Credentials

  Attribute           Description
  ------------------- ----------------------------------------------------
  Terminal ID         Unique terminal identifier issued for the merchant
  Terminal Password   Secure password issued for the terminal
  Merchant Key        Secret key used for request and response hashing

⚠️ **Important:** Keep the Merchant Key confidential.

------------------------------------------------------------------------

# Plugin Installation

## 1. Install Plugin

1.  Download the latest plugin **source code ZIP** from the Merchant
    Dashboard.
2.  Extract the plugin.
3.  Upload files to your **CS‑Cart installation directory**:

```{=html}
<!-- -->
```
    app/      → CS‑Cart app folder
    design/   → CS‑Cart design folder

4.  Login to **CS‑Cart Admin Panel**.
5.  Navigate to:

```{=html}
<!-- -->
```
    Settings → Payment Methods

6.  Click **+ Add Payment Method**.
7.  Select **AlinmaPay** from the list.
8.  Click **Save**.

------------------------------------------------------------------------

# Database Configuration

1.  Open **phpMyAdmin**.
2.  Select your **CS‑Cart database**.
3.  Click **SQL** tab.
4.  Upload and execute:

```{=html}
<!-- -->
```
    install_AlinmaPay.sql

------------------------------------------------------------------------

# Plugin Configuration

Navigate to the **Configuration Tab** and provide the following
parameters:

  Parameter                Description
  ------------------------ ------------------------------------------
  Merchant ID              Merchant account identifier
  Secret Key               Merchant secret key
  Transaction URL          Payment gateway transaction endpoint
  Transaction Status URL   Endpoint for checking transaction status
  Description              Payment method description

Click **Save & Close** after configuration.

------------------------------------------------------------------------

# Transaction Request Example

``` php
$fields = array(
    'terminalId' => $terminal_id,
    'password' => $password,
    'signature' => $hash,
    'paymentType' => $transaction_type,
    'amount' => $order_info['total'],
    'currency' => $currency,
    'order' => array(
        'orderId' => $order_info['order_id'],
        'description' => ""
    ),
    'customer' => array(
        'customerEmail'=> $order_info['email'],
        'billingAddressStreet'=> "",
        'billingAddressCity'=>"",
        'billingAddressState'=>"",
        'billingAddressPostalCode'=> "",
        'billingAddressCountry'=> $country
    )
);
```

------------------------------------------------------------------------

# Handling Payment Response

## 1. Configure Receipt URL

Example:

    http://localhost/cscart/index.php?dispatch=PAYMENT_NOTIFICATION.return&payment=AlinmaPay

Configure this URL in the **Merchant Portal**.

Example payload:

    "additionalDetails": {
    "userData":"{"entryone":"abc","receiptUrl":"http://localhost/cscart/index.php?dispatch=PAYMENT_NOTIFICATION.return&payment=AlinmaPay"}"
    }

------------------------------------------------------------------------

## 2. Read Encrypted Response

``` php
$jsonData = file_get_contents("php://input");
parse_str($jsonData, $parsedData);

unset($parsedData['termId']);

if (isset($parsedData['data'])) {
    $dataValue = $parsedData['data'];
    $decodedData = urldecode($dataValue);
    $decodedData = str_replace(' ', '+', $decodedData);
    $encryptedResponse = $decodedData;
}
```

------------------------------------------------------------------------

## 3. Decrypt Response

The response is encrypted using **AES‑256‑ECB** and must be decrypted
using the **Merchant Key**.

``` php
function decryptData($encryptedResponse, $merKey) {

    $binaryKey = hex2bin($merKey);
    $decodedData = base64_decode($encryptedResponse);

    $decryptedData = openssl_decrypt(
        $decodedData,
        'AES-256-ECB',
        $binaryKey,
        OPENSSL_RAW_DATA
    );

    return $decryptedData;
}
```

------------------------------------------------------------------------

## 4. Decode JSON Response

``` php
$data = json_decode($decryptedData, true);
```

------------------------------------------------------------------------

# Failed Transaction Handling

Edit the following file:

    design/themes/responsive/templates/views/checkout/complete.tpl

Add the following snippet:

``` html
{if $smarty.server.REQUEST_URI|strpos:'status=failure'}
<p>Transaction failed. Please try again.</p>
{else}
<p>{__("text_order_placed_successfully")}</p>
{/if}
```

------------------------------------------------------------------------

# Supported Payment Methods

The plugin currently supports:

-   Purchase
-   Authorization

------------------------------------------------------------------------

# API Specifications

Detailed **request and response formats** are available in the **API
Specification Document** on the Merchant Portal.

Location:

    Merchant Portal → Developer → API Keys → Developer Integration Guide

------------------------------------------------------------------------

# Logging & Troubleshooting

Logs are stored in:

    var/log/php_errors.log

Enable logging in:

    config.local.php

``` php
ini_set('log_errors', '1');
ini_set('error_log', DIR_ROOT . '/var/log/php_errors.log');
```

If logs are not generated, check:

    xampp/apache/logs/error.log

------------------------------------------------------------------------

# Support

For integration assistance, contact the **Alinma Pay Technical Support
Team** through the Merchant Portal.

------------------------------------------------------------------------
