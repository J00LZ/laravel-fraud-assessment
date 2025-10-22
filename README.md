# Fraud Detector Assessment
By Julius de Jeu

## Running the example
* Make sure that all the requirements for laravel are installed (see [this page](https://laravel.com/docs/12.x/installation))
* Run the `customers-api` docker container with it exposed at port `8080`
* Copy the `.env.example` file to `.env` and set the `APP_KEY` variable to a valid value (can be generated with `php artisan key:generate`)
* Execute `composer run dev` to run the development environment
* Go to http://localhost:8000

## What is implemented
* The basic requirements
  * A web page that has a way link to trigger the fraud detection process
  * The fraud detection itself
  * A table that displays the results of a scan
* Caching scan results
  * The home page displays the last executed scan by default, or it'll be empty if there are no scans done yet. 
* API
  * Under `/scans` a list of all scans can be found
  * `/scans/{id}/customers` has the list of scan customers, including if they are valid and the reasons why they are not valid if that is the case. 
* Tailwind: An ever so slightly better look
  * Design is in fact not my strong suit
* Reason display
  * The method `Customer::isValid` replies why the customer isn't valid if that is the case, or an empty array if the customer is valid. 
  * We only actually load the other matching rows for the IP and IBAN checks if we are using it for display, otherwise we simply check if there is more than one row that matches
  * If the `valid` field in the database row is `true` and we aren't writing the result to the database we simply skip all checks since we already have checked it. 
* Testing
  * Unit tests aren't my expertise, but I did add simple tests for the four reasons a customer could be fraudulent. 

## What isn't implemented
Mostly a better interface would be nice, 
maybe some line wrapping in case the list of names that share an IP or IBAN would be nice. 

## Trade-offs
The method that duplicate IP's and IBAN's are found is probably not optimal, since both have O(n^2) complexity. 
I can't think of a way to relieve this problem however. 

The database also does not include every field, which is not really a problem for now, but if you for instance want to add a method that validates the address of a customer is in The Netherlands, or check if the provided email address is valid you'd have to add that to the database, and still make sure that the old ones stay valid since they would have empty values in the new columns in the database. 

## If given more time, what could be improved
Mostly following code standards can be better, like I said in my first interview I've not really used Laravel or PHP seriously (PHP for a Wordpress plugin but that's quite different from Laravel). 
Not knowing the language and framework means I try to do things the way they work in other languages often, which sometimes works but looks weird, and sometimes doesn't work at all. 

Next to this I would spend quite a bit of time polishing the UI, while the current interface is usable, it can be better if I had more time. 

Somehow fix the fact that the customer api is quite slow, probably by adding a load bar or something like it. This would require me figuring out how to do active communication in both directions which I'm not sure is possible however. 

Add more commit messages at every "milestone". Since I was working on one PC and on my own I went back to my habit of not really making many commits, but making one when I made the project, one when I added the migrations, one when I got the validation working and so on could have made my progress easier to follow for others. 

## Other notes
I assumed that with "a trigger" any way that allows an action was meant, so I went with a simple web GET request. I did however just realise that Laravel also has an entire event framework, which I suppose could also have been used for this. 

Total time spent: about 8 hours total, spread out over 3 days. 