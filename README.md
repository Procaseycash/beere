    # beere Class (PDO & MYSQL Version), Born of PHP7 
    #
    #
    # Making Restful Query to JSON MYSQL
    # This 'Beere' Class is of PHP 7, other lower version might have problem to use the query methods.
    # This 'Beere' class reduces many mysqli query you need to do on every service request
    # this 'Beere' class helps to structure all operations of your app in one file.
    # it make use of array of key and value of which the key correspond to table field and value is content to save.
    # request are sent in array form and response is send in Json to the caller.
    # There are many operations this class of BeereOperations & BeerePdoOperations can perform to help develop a restful response service synchronously and asynchronously.
    # To save nested array of level 2, use saveMultiples.
    # The response returns json of five category of data, which are:
    # Status which is Integer to denote 200 for success and 400 for failure.
    # Message which is string, 
    # Data which  can either be json of data queried
    # Code is bool to denote success
    # Total: this is majorly used while using list based on limit. 
    # We now cover all execution of the Beere Class methods to query for restful response.
    # Kindly change the connection in utils folder to connect with your own database and create a table user with email, first_name, * status fields.
    # Study it well to see how powerful it is by using one or more together to achieve your goal. it is well optimised.
    # To use The MYSQLI VERSION: 
    # require beereOperation and instantiate the class.
    #
    # To use The PDO VERSION: 
    # require beerePdoOperation and instantiate the class.
    