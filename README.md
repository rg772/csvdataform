




Format of CSV file
-

Have a CSV formatted with three columns: The question, reponse type, and a slug. Don't use any column headers. They are not needed. 

| Column 1 (Label)      | Column 2 (Type)    | Column 3  (slug)   |
| :------------- | :---------- | :----------- |
| Your Name *	|   string	|       Name|
|E-mail Address *	|Email|	email|
|Name of Project *	|string|	project_name|
|Type of Project	|string	|project_type|
|Funding & Crew	|label|	|
|Amount Requested	|number|	amount|
|Have you received a previous grant? *	|boolean|	previous_award|
|Have you applied for other sources of money? *	|boolean|	other_money|

The first column is what appears on the screen in the `<label>` tag. 

The type column can have any of the following: email, text, boolean, link, string, or number. This value determines how schema and form boiler plate.

The slug is the column and input name. It is automatically created by the first 10 words of the label if none exists. 

`Required` inferred by ending the label with a asterisk.  


Schema Boilerplate
-
In tinker, execute the following

```php
use Symfony\Component\Console\Output\ConsoleOutput;
$filename = base_path() . '/database/dataform.csv';
(new ConsoleOutput())->writeln(soc\CSVDataForm::BuildSchemaBlock($filename));
```

To build this...
```php
// Start
$table->string('Your_Name');
$table->string('E-mail_Address');
$table->string('Name_of_Project');
$table->string('Type_of_Project')->nullable();
// Funding & Crew
$table->interger('Amount_Requested')->nullable();
```


Form Section Boilerplate
-
In tinker, execute:
```php
use Symfony\Component\Console\Output\ConsoleOutput;
$filename = base_path() . '/database/dataform.csv';
(new ConsoleOutput())->writeln(soc\CSVDataForm::BuildFormSegment($filename));
```

It should give something like below:
```html
<div class='form-header'>Start</div>
	<div class='Your_Name'> 
		 <label for='Your_Name'>Your Name *</label> 
 		 <input type="text" placeholder="Your Name *" name="Your_Name" required='true'/> 
	 </div>
	<div class='E-mail_Address'> 
		 <label for='E-mail_Address'>E-mail Address *</label> 
 		 <input type="email" placeholder="E-mail Address *" name="E-mail_Address" required='true'/> 
	 </div>
	<div class='Name_of_Project'> 
		 <label for='Name_of_Project'>Name of Project *</label> 
 		 <input type="text" placeholder="Name of Project *" name="Name_of_Project" required='true'/> 
	 </div>
	<div class='Type_of_Project'> 
		 <label for='Type_of_Project'>Type of Project</label> 
 		 <input type="text" placeholder="Type of Project" name="Type_of_Project" required='false'/> 
	 </div>
</div>
<div class='form-header'>Funding & Crew</div>
	<div class='Amount_Requested'> 
		 <label for='Amount_Requested'>Amount Requested</label> 
 		 <input type="number" placeholder="Amount Requested" name="Amount_Requested" required='false'/> 
	 </div>
.
.
.
</div>
```


Next
-
The goal is to have this compare against an existing table and produce boilder plate based on table differences. 
