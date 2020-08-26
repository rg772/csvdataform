


Call while in development
-
During development I called this via the following from `web.php`:

```php
Route::get('/', function () {
    dump( \soc\CSVDataForm::loadCSV( base_path() . '/database/dataform.csv'));
    return 'true';
});
```


Format of CSV file
-

The CSV is formatted in three columns intrepreted as 
- label - What appears on the screen in the `<label>` tag
- type
    - email
    - text (which converts to textarea)
    - boolean
    - link
    - string (which converts to `type=text`)
    - number
- slug - Automatically created by the first 10 words of the label. This is the data key name in the object

Do not have column headers in your CSV file. `Required` inferred by ending the label with a asterisk.  

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


Data Structure
-
```
array:7 [▼
  "Start" => array:4 [▼
    "Your_Name" => array:4 [▼
      "label" => "Your Name *"
      "type" => "string"
      "required" => true
      "input" => "<label for='Your_Name'>Your Name *</label><input type="text" placeholder="Your Name *" name="Your_Name" required='true'/>"
    ]
    "E-mail_Address" => array:4 [▼
      "label" => "E-mail Address *"
      "type" => "email"
      "required" => true
      "input" => "<label for='E-mail_Address'>E-mail Address *</label><input type="email" placeholder="E-mail Address *" name="E-mail_Address" required='true'/>"
    ]
    "Name_of_Project" => array:4 [▶]
    "Type_of_Project" => array:4 [▶]
```
