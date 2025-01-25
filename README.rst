Qerbia QUUID
============

Qerbia QUUID is a specialized UUID (Unique Identifier) library that provides a custom UUID8 implementation with embedded integer encoding capabilities.

.. contents::

Features
--------

- Generate custom UUID8-compatible identifiers
- Encode and decode integer IDs within UUID strings
- Supports unique identifier generation with embedded data
- Strictly validates QUUID format

Requirements
------------

- PHP 7.1+
- Supports strict typing

Installation
------------

.. code-block:: bash

   composer require qerbia/quuid

Usage
-----

Decoding a QUUID
~~~~~~~~~~~~~~~~

.. code-block:: php

   <?php

   use Qerbia\Quuid\Quuid;

   $quuid = new Qerbia\Quuid\Quuid();
   $id = $quuid->decode('0d1b4268-e341-8873-9661-185378350c35');
   // Returns an integer ID (e.g., 15)

Supported Ranges
~~~~~~~~~~~~~~~~

- Minimum Encoded ID: 0
- Maximum Encoded ID: 4,294,967,295 (32-bit unsigned integer)

Validation
~~~~~~~~~~

The library provides strict validation for:

- UUID8 format compliance
- QUUID character restrictions
