<?php

$conn = mysqli_connect("localhost","root","","energibot");

if($conn){
$user_messages = mysqli_real_escape_string($conn, $_POST['messageValue']);

$query = "SELECT * FROM chatbot WHERE messages LIKE '%$user_messages%'";
$runQuery = mysqli_query($conn, $query);

if(mysqli_num_rows($runQuery) > 0){
    // fetch result
    $result = mysqli_fetch_assoc($runQuery);
    // echo result
    echo $result['response'];
}else{
    echo "I'm sorry, I'm not sure what you are asking for. Can you please provide more context or clarify your question?<br/><br/>Choose the appropriate word for your query:
	<br>
	<br>- General Inquiry
	<br>- Request new connection
	<br>- GPOBA Connections
	<br>- Check Power Status
	<br>- Inquire About Billing
	<br>- Payment Assistance
	<br>- Complaint about billing
	<br>- Sales Inquiry 	
	<br>- Inquire about bill payment
	<br>- Technical Assistance
	<br>- Request for energy-saving tips
	<br>- Complaint about power quality
	<br>- Report power outage
	<br>- Request for outage information
	<br>- Key Products and Services
	<br>- Regional Marketing Activities
	<br>- Market Research
	<br>- Demand Creation
	<br>- Sales Growth
	<br>- Administration Related Queries
	<br>- Get Information on Last Mile Connectivity Program
	<br>- Inquire about Stima Loan
	<br>- Customer Satisfaction
	<br/>
	<br/>
	If you can't find your query here, please fill the form: <a href='https://forms.gle/nimXmrjyRryivJYd9' style='text-decoration: none; color: white'> <u>Click to add your query</u></a>";
}
}else{
    echo "connection Failed " . mysqli_connect_errno();
}
?>