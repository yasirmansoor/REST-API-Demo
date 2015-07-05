Yasir Mansoor
05/07/2015

This is my take on a REST API, and has the following features:

- JSON router to configure each endpoint with optional security token (time-limited), HTTPS,  allowable actions (individually enable PUT, POST, GET or DELETE) and ability to assign additional data parameters (for compounded filters on data result)

- URI/POST santisation. 

- Easy extensibility by adding an endpoint to the router config, creating an empty child class with the endpoint name, and then creating an associated JSON data file with the same name.

- Utilises Factory, Singleton and MVC design patterns.

- Caching (APCu).

- Generate security tokens according to consumer.

- Data source is JSON, but can easily be changed to anything else.

- Output format as JSON or XML.

Usage
-----

1. Generate security token:

index.php?controller=config&get_security_token=<company_name_here>
(Company name can be found in Data/consumer_security_keys.json)

Each endpoint URL needs to have the "security_token" and "company_name_here" supplied if the router is configured to enable secrutiy tokens.

2. Customers endpoint:

index.php?controller=customers&format=<json|xml>&id=<id>&security_token=<your_security_token>&consumer=<company_name_here>
(Customer IDs can be found in Data/customers.json)
(Data/customers_default.json can be used to reset data by replacing contents in Data/customers.json)

3. Orders endpoint:

a) Get order by ID:
index.php?controller=orders&format=<json|xml>&id=<customer_id>&security_token=<your_security_token>&consumer=<company_name_here>

b) Get orders by customer ID:
index.php?controller=orders&format=<json|xml>&customer_id=<customer_id>&security_token=<your_security_token>&consumer=<company_name_here>

c) Get orders by customer ID and date:
index.php?controller=orders&format=<json|xml>&customer_id=<customer_id>&date=<ddmmyyyy>security_token=<your_security_token>&consumer=<company_name_here>


