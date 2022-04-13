import { serve } from "https://deno.land/std@0.120.0/http/server.ts";
const html = sfsqlResultPayload => `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>LOW CODE SFSQL SHOP</title>
</head>
<body>
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
th, td {
  padding: 15px;
}
</style>
<pre id="whereToPrint"></pre>
<div id="examplewrap"></div>
  <script>
    var results = \`${sfsqlResultPayload}\`
     var obj = JSON.parse(results);
	 
	 let sups = obj.filter(supp => supp._q == "supp")[0]["data"];
	 let prods = obj.filter(prod => prod._q == "prod")[0]["data"]; 
	 let custs = obj.filter(cust => cust._q == "cust")[0]["data"]; 
	 let items = obj.filter(order => order._q == "order")[0]["data"]; 
	 let items1 = items.filter(obj => {
				return obj.orderid === 1;
			});
	 let items2 = items.filter(obj => {
				return obj.orderid === 2;
			});


let destEle = document.getElementById('examplewrap');
if (!destEle) {
	alert('could not find element examplewrap');
	exit;
}
	 
// Suppliers
//examplewrap.innerHTML += ("test"); 
document.write("<table>");
document.write("<tr><td colspan=" + sups.length + "><b>SUPPLIERS</b></td></tr>");	
document.write("<tr>"); 
for (var j = 0; j < sups.length; j++){
       document.write("<td>");	 
	 Object.entries(sups[j]).forEach(entry => {
		  const [key, value] = entry;
		  //console.log(key, value);
		  document.write(key + ": " +  value + "<br>");  
		});
		   document.write("</td>");	
	} 
document.write("</tr></table>"); 	

document.write("<br>"); 


//Products
document.write("<table>"); 
document.write('<tr><td colspan="' + prods.length + '"><b>PRODUCTS</b></td></tr>');	
document.write("<tr>"); 
for (var j = 0; j < prods.length; j++){
       document.write("<td>");	 
	 Object.entries(prods[j]).forEach(entry => {
		  const [key, value] = entry;
		  if(key== 'image'){ 
		   document.write('<img src="http://sfsqlblogimages.s3.amazonaws.com/product-images/'+ value +'"/><br>'); 
			}else{
			document.write(key + ": " +  value + "<br>");  
			}
		});
		   document.write("</td>");	
	} 
document.write("</tr></table>");

	document.write("<br>"); 
	
//Customers
document.write("<table>"); 
document.write('<tr><td colspan="' + custs.length + '"><b>Customers</b></td></tr>');	
document.write("<tr>"); 
for (var j = 0; j < custs.length; j++){
       document.write("<td>");	 
	 //Object.entries(custs[j]).forEach(entry => {
		 // const [key, value] = entry;
		 // document.write(key + ": " +  value + "<br>");  
		//});
		document.write("First Name : " + custs[j]['fname'] + "<br>"); 
		document.write("Last Name: " + custs[j]['lname'] + "<br>"); 
		document.write("ID: " + custs[j]['cusid'] + "<br>");
		document.write("Email: " + custs[j]['email'] + "<br>");	
      document.write("Address <br>"); 
      document.write(custs[j]['street'] + "<br>");
      document.write(custs[j]['city'] + " " + custs[j]['state'] + " " + custs[j]['zip']);	
		document.write("</td>");	
	} 
document.write("</tr></table>");
	
	
//Order	1	
document.write("<br><br>"); 
	
document.write("<table>"); 
document.write('<tr><td colspan="7"><b>Order 1</b></td></tr>');	
document.write("<tr>"); 
document.write('<td colspan="7">Order ID: ' + items1[0]['orderid'] + '<br>Customer: ' + items1[0]['fname'] + ' ' + items1[0]['lname'] +'<br>Email: '+ items1[0]['email'] +'<br>Cust ID: '+ items1[0]['cusid'] +'<br>Address:<br>');
document.write(items1[0]['street'] + "<br>" + items1[0]['city'] + " " + items1[0]['state'] + "  " + items1[0]['zip']);
document.write("</td></tr>"); 
document.write('<tr><th>Item Number</th><th>Product</th><th>Code</th><th>Manufacturer</th><th>Quantity</th><th>Unit Price</th><th>Subtotal</th></tr>');	
var t = 0;
for (var j = 0; j < items1.length; j++){
      document.write("<tr>"); 
		document.write("<td>" + items1[j]['no'] + "</td>"); 
		document.write('<td><img src="http://sfsqlblogimages.s3.amazonaws.com/product-images/' + items1[j]['image'] + '"/><br>' + items1[j]['name'] + '</td>'); 
	   document.write("<td>" + items1[j]['code'] + "</td>"); 
		document.write("<td>" + items1[j]['manname'] + "</td>");
		document.write("<td>" + items1[j]['quantity'] + "</td>"); 
		document.write("<td>" + items1[j]['price'] + "</td>"); 
		document.write("<td>" + items1[j]['price'] * items1[j]['quantity'] + "</td>"); 
		document.write("</tr>");
		var subtot = items1[j]['price'] * items1[j]['quantity'];
      t += subtot;	
	} 
document.write('<tr><td colspan="6" align="right">Total:</td><td>' + t + '</td></tr>');
document.write("</table>");

//Order	2
document.write("<br><br>"); 
	
document.write("<table>"); 
document.write('<tr><td colspan="7"><b>Order 2</b></td></tr>');	
document.write("<tr>"); 
document.write('<td colspan="7">Order ID: ' + items2[0]['orderid'] + '<br>Customer: ' + items2[0]['fname'] + ' ' + items2[0]['lname'] +'<br>Email: '+ items2[0]['email'] +'<br>Cust ID: '+ items2[0]['cusid'] +'<br>Address:<br>');
document.write(items2[0]['street'] + "<br>" + items2[0]['city'] + " " + items2[0]['state'] + "  " + items2[0]['zip']);
document.write("</td></tr>"); 
document.write('<tr><th>Item Number</th><th>Product</th><th>Code</th><th>Manufacturer</th><th>Quantity</th><th>Unit Price</th><th>Subtotal</th></tr>');	
var t = 0;
for (var j = 0; j < items2.length; j++){
      document.write("<tr>"); 
		document.write("<td>" + items2[j]['no'] + "</td>"); 
		document.write('<td><img src="http://sfsqlblogimages.s3.amazonaws.com/product-images/' + items2[j]['image'] + '"/><br>' + items2[j]['name'] + '</td>'); 
	   document.write("<td>" + items2[j]['code'] + "</td>"); 
		document.write("<td>" + items2[j]['manname'] + "</td>");
		document.write("<td>" + items2[j]['quantity'] + "</td>"); 
		document.write("<td>" + items2[j]['price'] + "</td>"); 
		document.write("<td>" + items2[j]['price'] * items2[j]['quantity'] + "</td>"); 
		document.write("</tr>");
		var subtot = items2[j]['price'] * items2[j]['quantity'];
      t += subtot;	
	} 
document.write('<tr><td colspan="6" align="right">Total:</td><td>' + t + '</td></tr>');
document.write("</table>");
document.write("<br><br>"); 
    
  </script>
 </body>
	 </html>`;
	 const endpoint = Deno.env.get('endpont');
  async function handler(req: Request): Promise<Response> {
  const resp = await fetch("https://api.sfsql.io/uhv4hkrp/api/v1/run", {
    method: "POST",
    headers: {
       "content-type": "application/json",
       "x-sfsql-apikey": Deno.env.get('api_key')
    },
    body: JSON.stringify([{"delete":{"objfilter":"SELECT $o:shopRoot.attrset('delete')"}},{"purge":{}},{"modify":{"data":{"shopRoot":{"INV":{"supplier":{"name":"Sony Group Corporation","id":"sony"},"product":[{"name":"FinePix Pro2 3D Camera","code":"3DcAM01","image":"camera.jpg","n:price":300.0,"i:totalordered":0,"manufacturer":{"#ref":"SELECT $o:.supplier.oid() WHERE $s:.supplier.id='sony'"}},{"name":"EXP Portable Hard Drive","code":"USB02","image":"external-hard-drive.jpg","n:price":800.0,"i:totalordered":0,"manufacturer":{"#ref":"SELECT $o:.supplier.oid() WHERE $s:.supplier.id='sony'"}},{"name":"Luxury Ultra thin Wrist Watch","code":"wristWear03","image":"watch.jpg","n:price":100.0,"i:totalordered":0,"manufacturer":{"name":"TAG Heuer","id":"tag"}},{"name":"XP 1155 Intel Core Laptop","code":"LPN45","image":"laptop.jpg","n:price":250.0,"i:totalordered":0,"manufacturer":{"name":"DELL Inc","id":"dell"}}],"o:supplier":[{"#append":{}},{"#ref":"SELECT $o:.manufacturer.oid() WHERE $s:.manufacturer.id='tag'"},{"#ref":"SELECT $o:.manufacturer.oid() WHERE $s:.manufacturer.id='dell'"}]},"customer":{"#set":{"where":"$s:customer.id='c1111'"},"s:id":"c1111","s:first_name":"Larry","s:last_name":"Smith","o:address":{"street":"5 Elmwood Avenue","city":"Rochester","state":"NY","zip":"14616"}},"o:order":[{"#append":{}},{"i:order_id":1,"d:datetime":"now","o:cust":{"#ref":"SELECT $o:.customer.oid() WHERE $s:.customer.id='c1111'","email":"support@schemafreesql.com"},"o:lineitem":[{"o:item":{"#ref":"SELECT $o:.product.oid() WHERE $s:.product.code='3DcAM01'","i:totalordered":{"#pass":"v + 1"}},"i:no":1,"i:qty":1,"n:price":300.0},{"o:item":{"#ref":"SELECT $o:.product.oid() WHERE $s:.product.code='wristWear03'","i:totalordered":{"#pass":"v + 2"}},"i:no":2,"i:qty":2,"n:price":100.0}]},{"i:order_id":2,"d:datetime":"now","o:cust":{"s:id":"c2222","s:first_name":"Sally","s:last_name":"Swanson","email":"feedback@schemafreesql.com","o:address":{"street":"7 Broadway","city":"New York","state":"NY","zip":"10003"}},"o:lineitem":[{"o:item":{"#ref":"SELECT $o:.product.oid() WHERE $s:.product.code='LPN45'","i:totalordered":{"#pass":"v + 2"}},"i:no":1,"i:qty":2,"n:price":250.0}]}],"o:customer":[{"#append":{}},{"#ref":"SELECT $o:.cust.oid() WHERE $s:.cust.id='c2222'"}]}}}},{"query":{"sfsql":"SELECT $s:.supplier.name as name, $s:.supplier.id as id","_q":"supp"}},{"query":{"sfsql":"SELECT $s:.product.code as code,$s:.product.name as name,$s:.product.image as image,$n:.product.price as price,$i:.product.totalordered as totordered,$s:.product.manufacturer.name as manname","_q":"prod"}},{"query":{"sfsql":"SELECT $s:.customer.id as cusid,$s:.customer.first_name as fname,$s:.customer.last_name as lname,$s:.customer.email as email,$s:.customer.address.street as street,$s:.customer.address.city as city,$s:.customer.address.state as state,$s:.customer.address.zip as zip","_q":"cust"}},{"query":{"sfsql":"SELECT $i:.order.order_id as orderid, $s:.order.lineitem.item.code as code,$s:.order.lineitem.item.name as name,$s:.order.lineitem.item.manufacturer.name as manname, $s:.order.lineitem.item.image as image, $i:.order.lineitem.no as no, $i:.order.lineitem.qty as quantity, $n:.order.lineitem.price as price, $s:.order.cust.id as cusid,$s:.order.cust.first_name as fname, $s:.order.cust.last_name as lname,$s:.order.cust.email as email,$s:.order.cust.address.street as street, $s:.order.cust.address.city as city, $s:.order.cust.address.state as state,  $s:.order.cust.address.zip as zip","_q":"order"}},{"delete":{"objfilter":"SELECT $o:shopRoot.attrset('delete')"}},{"purge":{}}])
  });

  const data = await resp.json();


  const body = html(JSON.stringify(data));
  return new Response(body, {
    headers: { 'content-type': 'text/html',
				'content-length': body.length },
  });

  
}

console.log("Listening on http://localhost:8000");
serve(handler);
