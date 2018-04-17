# doctrine-base-entities

This Zend module provides base classes to simplify handling of doctrine entities. Currently it consists of

- two abstract entity classes
- one general repository class
- one service class

[![Latest Version](https://img.shields.io/github/release/omanshardt/doctrine-base-entities.svg?style=flat-square)](https://github.com/omanshardt/doctrine-base-entities/releases) [![Total Downloads](https://img.shields.io/packagist/dt/omanshardt/doctrine-base-entities.svg?style=flat-square)](https://packagist.org/packages/omanshardt/doctrine-base-entities) [![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

Install
-------
```bash
composer require omanshardt/doctrine-base-entities
```

## Entity classes

- `MBBaseEntity` simplifies handling of creation and modification date by providing a `created` and a `modified` property along with its accessor methods as well as appropriate lifecycleevent methods to set creation and modification date on persist and modification date on update. So this has not to be implemented in every entity class on it's own.
In addition this class defines an abstract class `getTextIdentifier()` that is very similar to an `getString()` method and that should return a suitable string representation of the entity.

- `MBInterleavedChecksumEntity` creates a simple checksum from provided record data as well as an interleaved checksum from provided record data alongside with data of the forerunning record (the predecessor). It defines an abstract method `getInterityData()` that needs to be implemented in the concrete entity class and returns an array of data that should be included within the checksum. This class also saves the id of it's predecessor into the current record.

## Repository class
- `MBInterleavedChecksumEntityRepository` provides the `findLatest()` method that returns the latest record within the corresponding table. Currently it does this by id (what means that this only works as intended if id is incremental).

## Service class

- `IntegrityService` provides the `getValidatedEntities()` method that returns a list of all entities with their validity checked. This result is similar to the repository's method `findAll()` but with validation and is needed as validation is a tiime consuming process and should not happen on every findAll()` operation.

## Usage
To use `MBBaseEntity` add the following fields to the table that is related to the entity.

- `created (DateTime)`
- `modified (DateTime)`

Extend the `MBBaseEntity` within the concrete entity class. Add a `getTextIdentifier()` method to the concrete class that returns a suitable string representation of the entity.

To use `MBInterleavedChecksumEntityRepository` do everything that has to be done for using `MBBaseEntity` (this is because `MBInterleavedChecksumEntityRepository` extends `MBBaseEntity`) and add the following fields to the table that is related to the entity.

- `simple_checksum (varchar(1000))`
- `interleaved_checksum (varchar(1000))`
- `predecessor_id (int)`

Extend the `MBInterleavedChecksumEntityRepository` within the concrete entity class. Add a `getInterityData()` method to the concrete class that returns an array of data that should be part of the simple and interleaved checksum.