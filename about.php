<?php
    echo '<p style="border-bottom:#ccc thin solid; font-weight:bold;"><a style="color:#56c;" href="home.php">Home</a></p>';
    echo    '<blockquote align="justify" style="color:#000;padding:10px;"><fieldset><legend><h3 style="color:#56c;">Description</h3></legend>
            This website scraps the tabular data from the website specified by the URL and stores it into the database.
            First, it displays the total tables residing inside the website whose URL is entered by the user. Then the user              should choose the table number where the required data is. In addition to this, user should provide the header                pattern which basically represents whether there is a header in the table or not. If there exists header, then                it is also customary to provide the depth within which the header data is placed. Say for e.g. if header is                  placed like this;<b>           &lt;tr&gt;&lt;td&gt;My Headerv&lt;/td&gt;&lt;/tr&gt;</b>, Header Depth=2. Thus Header Pattern is Double. There is also an option for saving the tabular data as a CSV file. 
            </fieldset></blockquote>
            
            <blockquote align="justify" style="color:#000;padding:10px;"><fieldset>
            <legend><h3 style="color:#56c;">Team</h3></legend>We developed this product as the fulfilment of our database project. We are a team of three;
            <ol>
                <li>Kailash Budhathoki, IOE Pulchowk</li>
                <li>Hari Pradhan, IOE Pulchowk</li>
                <li>Sujan Pradhan, IOE Pulchowk</li>
            </ol>       
            </fieldset></blockquote>';
?>