const fetch = require('node-fetch');
const html = todos => `<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Todos</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet"></link>
  </head>
  	   <style>
/* unvisited link */
a:link {
  color: red;
}

/* visited link */
a:visited {
  color: green;
}

/* mouse over link */
a:hover {
  color: hotpink;
}

/* selected link */
a:active {
  color: blue;
}
		   
{
  box-sizing: border-box;
}
textarea {
  border-color: #eee;
  border-width: 1px;
  margin: 10px  10px 10px 6px
}
label, textarea, input {
  display: block;
   margin: 10px  10px 10px 6px;
}
input {
  border-style: solid;
  border-width: 1px;
  margin: 10px  10px 10px 6px;
}
.link {
  cursor:pointer;
  color:blue;
  text-decoration:underline;
}
.link-through {
  cursor:pointer;
  color:blue;
  text-decoration: line-through;
}
.details{
   margin: 10px  10px 10px 6px;
   -moz-box-shadow: 0 0 3px #ccc;
   -webkit-box-shadow: 0 0 3px #ccc;
   box-shadow: 0 0 3px #ccc;
   display: none;
}
.desc{
   margin: 10px  10px 10px 6px;
   border:1px solid black;
}
</style>
  <body class="bg-blue-100">
				<div id="focus"></div>
				  <div  id="tododetail" class="details">
					  <span id="todoinput"></span>
					  <b>Details</b> <br>
					  <div class="desc" id="tododesc" contentEditable="true">
						
					  </div>
					  <button onclick="updateTodo()" class="bg-blue-500 hover:bg-blue-800 text-white font-bold ml-2 py-2 px-4 rounded focus:outline-none focus:shadow-outline" id="updatetodo">Update</button>
					  <a href="#" onclick="closeDetail()">close<a>
				  </div>
					 
				   <div  class="w-full h-full flex content-center justify-center mt-8">
						<div class="bg-white shadow-md rounded px-8 pt-6 py-8 mb-4">
						  <h1 class="block text-grey-800 text-md font-bold mb-2">Todos</h1>
						  <div id="create" class="flex">
							 <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-800 leading-tight focus:outline-none focus:shadow-outline" type="text" name="name" placeholder="A new todo"></input>
							 <button class="bg-blue-500 hover:bg-blue-800 text-white font-bold ml-2 py-2 px-4 rounded focus:outline-none focus:shadow-outline" id="create" type="submit">Create</button>
						  </div>
						  <div class="mt-4" id="todos"></div>
						</div>
					</div>
  </body>
  <script>
  
function closeDetail(){
var x = document.getElementById("tododetail");
var y = document.getElementById("create");
x.style.display = "none";
y.style.display = "block";
}

function todoDetail(evt){
 var click = evt.target
 var todoElement = click.parentNode
 var newTodoSet = [].concat(window.todos)
 var todo = newTodoSet.find(t => t.oid == todoElement.dataset.todo)
var x = document.getElementById("tododetail");
var y = document.getElementById("create");
x.style.display = "block";
y.style.display = "none";
var todoContainer = document.querySelector("#todoinput")
todoContainer.innerHTML = null
var el = document.createElement("div")
el.dataset.todo = todo.oid
var input = document.createElement("input")
input.type = "text"
input.value = todo.name
input.id="todoname"
input.name="todoname"
var checkbox = document.createElement("input")
checkbox.type = "checkbox"
checkbox.id="todocompleted"
checkbox.name="todocompleted"
checkbox.checked = todo.completed ? 1 : 0
var hidden = document.createElement("input")
hidden.type = "hidden"
hidden.value = todo.oid
hidden.id="todooid"
hidden.name="todooid"
el.appendChild(input)
el.appendChild(checkbox)
el.appendChild(hidden)
todoContainer.appendChild(el)
document.getElementById('tododesc').textContent = todo.desc
document.getElementById("focus").scrollIntoView();
} 
    window.todos = ${todos}

    var updateTodos = function(updatetodo) {

	  var req = new XMLHttpRequest();
      req.open('POST','https://us-east1-sfsql-347312.cloudfunctions.net/sfsqltodo', false);
      req.setRequestHeader('Content-Type', 'application/json');
	     req.send(JSON.stringify(updatetodo));

        if(req.status == 200) {
          var data = JSON.parse(req.responseText);
        }	

        const defaultData = {todos: [] };
        if (typeof data[1]["data"] !== 'undefined'){
            var todos =  data[1]["data"]      
        }else{
           var todod = defaultData.todos
        }
          populateTodos(todos)
 }

    var updateTodo = function() {
	    var name = document.getElementById("todoname").value;
	    var oid = document.getElementById("todooid").value;
	    var completed = document.getElementById("todocompleted").checked;
      var desc = document.getElementById("tododesc").textContent;
	    todo = { name: name, completed: completed, oid: oid, desc: desc }
      updateTodos(todo)
    }

    var completeTodo = function(evt) {
      var checkbox = evt.target
      var todoElement = checkbox.parentNode
      var newTodoSet = [].concat(window.todos)
      var todo = newTodoSet.find(t => t.oid == todoElement.dataset.todo)
      todo.completed = !todo.completed
      updateTodos(todo)
    }

var populateTodos = function(newtodos) {
	    window.todos = newtodos
    var todoContainer = document.querySelector("#todos")
      todoContainer.innerHTML = null
      window.todos.forEach(todo => {
        var el = document.createElement("div")
        el.className = "border-t py-4"
        el.dataset.todo = todo.oid
        var name = document.createElement("span")
		name.className = "link"
        name.className = todo.completed ? "link-through" : "link"
        name.textContent = todo.name
		name.addEventListener('click', todoDetail)
        var checkbox = document.createElement("input")
        checkbox.className = "mx-4"
        checkbox.type = "checkbox"
        checkbox.checked = todo.completed ? 1 : 0
        checkbox.addEventListener('click', completeTodo)
        el.appendChild(checkbox)
        el.appendChild(name)
        todoContainer.appendChild(el)
      })
    }
    populateTodos(window.todos)
	 
    var createTodo = function() {
      var input = document.querySelector("input[name=name]")
      if (input.value.length) {
        todo = { name: input.value, completed: false, oid: 0 }
        input.value = ""
        updateTodos(todo)
      }
    }
	 
    document.querySelector("#create").addEventListener('click', createTodo)
  </script>
</html>`
const api = process.env.api_key
const url = process.env.endpoint
const defaultData = { todos: [] }

exports.sfsqltodo = async (_req, res) => {
const ipa =  _req.headers['x-appengine-user-ip'] || _req.header['x-forwarded-for'] || _req.connection.remoteAddress  
const ip = ipa.replaceAll('.', '').replaceAll(':', ''); 
    if (_req.method === 'POST') {
                    const todos = _req.body
                    const oid = todos.oid
                    const todostring = JSON.stringify(todos);
                    const putData =`[{"modify":{"data":{"o:cftodos":{"${ip}":{"todos":[{"#set":{"where":"$o:todos.oid()=${oid}"}},${todostring}]}}}}},{"query":{"sfsql":"SELECT $o:.${ip}.todos.oid() as oid, $s:.${ip}.todos.name as name, $b:.${ip}.todos.completed as completed,$$s:.${ip}.todos.desc as desc ORDER BY oid ASC"}}]`;
                    const body = putData
                    const init = {
                      body: body,
                      method: 'POST',
                      headers: {
                        'content-type': 'application/json;charset=UTF-8',
                        'x-sfsql-apikey': api,
                      },
                    };                    
                    try {
                      const response = await fetch(url,init);
                      const json = await response.json();
                      const data =  JSON.stringify(json);
                      res.set('Content-Type', 'application/json;charset=UTF-8');
                      res.status(200).send(data); 
                    
                      } catch (error) {
                      res.status(500).send(error);
                      }            
    }else{
            const QData = `[{"query":{"sfsql":"SELECT $o:.${ip}.todos.oid() as oid, $s:.${ip}.todos.name as name, $b:.${ip}.todos.completed as completed,$$s:.${ip}.todos.desc as desc ORDER BY oid ASC"}}]`
            const init = {
              method: 'POST',
              body: QData,
              headers: {
                      'Content-Type': 'application/json',
                    'x-sfsql-apikey': api
                      }
              };
              try {
                const response = await fetch(url,init);
                const data = await response.json();
                if(data[0]["data"] == undefined){
                const todos = defaultData.todos
                const body = html(JSON.stringify(todos || []).replace(/</g, "\\u003c"))
                            res.status(200).send(body); 
                }else{
                 const todos = data[0]["data"];	
                 const body = html(JSON.stringify(todos || []).replace(/</g, "\\u003c"))
                            res.status(200).send(body); 
                }         
            } catch (error) {
              res.status(500).send(error);
            }                   
        };
}