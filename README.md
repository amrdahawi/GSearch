# GSearch
==========

This project is implemented using Symfony Framework and using google custom search api.

__Please insure that you have your custome search api key and cx ready before setting up the project__

Clone the project from git repo https://github.com/amrdahawi/GSearch.git to your local machine.

run composer update, this will install all the required bundles for symfony and GSearch Bundle

while composer is running, you can skip the details for the DB connection but insure to update the parameters for google api key and cx

When the composer is done, run the server using the below command:
php bin/console server:run

once server is up and running, you browse to the below url:

localhost:8000

# Project structure:

## app/config folder:
    This folder contains all the config files for the project
    - config.yml is the main config file
    - parameters.yml contains all required project parameters
    - routing.yml contacting the routing information for the project, in this case it points to the GSearch bundle annotation

## app/Resources folder:
    This folder contains the base views twig file

## src folder:
        - Contains the GSearch bundle code

### Controller:
        - controller for main page

### Resources:
        - Contains the services.yml under config folder, service definition file
        - contains all view twig files under views folder

### Service:
        - Contains the search Service class, which is responsible for executing google search
        - Contains the APIClient service class, which is responsible for making google custom search api calls

### Validator:
        - contains validation service to validate the provided input for the search

### tests folder:
    contains a unit test for the singleSearch function in the search service class. This is a simple test that is
    just checking the structure output.

    To run the unit test, execute:
    php vendor\phpunit\phpunit\phpunit SearchTest tests\GSearchBundle\Service\SearchTest.php


## Implementation assumptions/information:

- The design pattern followed is MVC, also following SOLID principle
- There is no db model in this case as I am not saving any data
- The model is more of a service (search service). where apiClient service is injected into it
- can handle multiple keywords and urls by adding more keyword/url fields on the Form.
- validation is done on urls, requires a valid url (starting with a scheme).
- Limit the number of keywords/urls per search to 3 for performance reasons

## Future Improvements

- save data to db for history tracking
- Use Factory pattern for creating entities when saving data to DB
- Performance improvements, google custom search limits the number of results to 10 per call, which means that we need to make 10 calls to get the first 100 results,
we also need to make multiple calls for multiple keywords which degrades performance even more.
one solution for this is to make asynchronous get calls for different keywords.
