const fetch = require("node-fetch");
const API_ENDPOINT = process.env.endpoint;

exports.handler = async (event, context) => {
//console.log(`{\ncontext: ${JSON.stringify(context,null,2)},\nevent: ${JSON.stringify(event,null,2)}\n}`);
const ipa = event.headers['x-nf-client-connection-ip'];
const ip = ipa.replace(/\./g, '').replace(/\:/g, '');	
const QData = `[{"query":{"sfsql":"SELECT $i:.${ip}.todos.id as id, $s:.${ip}.todos.name as name, $b:.${ip}.todos.completed as completed"}}]`;
	if (event.httpMethod == "GET") {	
		
	if(event.queryStringParameters.id){
		
		  const id = event.queryStringParameters.id
		  return {
        statusCode: 302,
        headers: {
            "Location": "/?id=" + id,
                 },
         };
			
		 }else{
			 
				return fetch(API_ENDPOINT, {
					  headers: {
							"content-type": "application/json",
							"x-sfsql-apikey": process.env.api_key
						  }, 
						  method: "POST",
						  body: QData,
					  })
					 .then((response) => response.json())
					 .then((data) => ({
						statusCode: 200,
						body:JSON.stringify(data),
					 }))
					 .catch((error) => ({ statusCode: 422, body: String(error) }));
		 }		 
	}else{
			const updatedtodo = JSON.parse(event.body)
			const id = updatedtodo.todos.id;
			const todostring=JSON.stringify(updatedtodo.todos);
			const putData = `[{"modify":{"data":{"o:cftodos":{"${ip}":{"todos":[{"#set":{"where":"$i:todos.id=${id}"}},${todostring}]}}}}}]`
			return fetch(API_ENDPOINT, {
					  headers: {
							"content-type": "application/json",
							"x-sfsql-apikey": process.env.api_key
						  }, 
						  method: "POST",
						  body: putData,
					  })
					 .then((response) => response.json())
					 .then((data) => ({
						statusCode: 200,
						body:JSON.stringify(data),
					 }))
					 .catch((error) => ({ statusCode: 422, body: String(error) }));
			
			
		} 
	  
	 
};	 