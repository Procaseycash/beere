# beere
# Making Restful Query to JSON MYSQL
# This 'Beere' Class is of PHP 7, other lower version might have problem to use the query methods.<br>
# This 'Beere' class reduces many mysqli query you need to do on every service request<br>
# this 'Beere' class helps to structure all operations of your app in one file.<br>
# it make use of array of key and value of which the key correspond to table field and value is content to save.<br>
# request are sent in array form and response is send in Json to the caller.<br>
# There are many operations this class of BeereOperations can perform to help develop a restful response service synchronously and asynchronously.<br>
# To save nested array of level 2, use saveMultiples.<br>
# The response returns json of five category of data, which are:<br>
# Status which is Integer to denote 200 for success and 400 for failure.<br>
# Message which is string, <br>
# Data which  can either be json of data queried<br>
# Code is bool to denote success<br>
# Total: this is majorly used while using list based on limit. <br>
# We will cover a limited execution of queries with CRUD using our class.<br>
# Kindly change the connection in utils folder to connect with your own database and create a table user with email, first_name, * status fields.<br>
# <br><b>Study it well to see how powerful it is by using one or more together to achieve your goal. it is well optimised.</b>
#