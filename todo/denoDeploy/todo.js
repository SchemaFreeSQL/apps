import { serve } from "https://deno.land/std@0.120.0/http/server.ts";
import { getIP } from "https://deno.land/x/get_ip/mod.ts";

const html = todos => `
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Todos</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet"></link>
  </head>
  <body class="bg-blue-100">
    <div class="w-full h-full flex content-center justify-center mt-8">
      <div class="bg-white shadow-md rounded px-8 pt-6 py-8 mb-4">
        <h1 class="block text-grey-800 text-md font-bold mb-2">Todos</h1>
        <div class="flex">
          <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-800 leading-tight focus:outline-none focus:shadow-outline" type="text" name="name" placeholder="A new todo"></input>
          <button class="bg-blue-500 hover:bg-blue-800 text-white font-bold ml-2 py-2 px-4 rounded focus:outline-none focus:shadow-outline" id="create" type="submit">Create</button>
        </div>
        <div class="mt-4" id="todos"></div>
      </div>
    </div>
  </body>
  <script>
    window.todos = ${todos}
    var updateTodos = function() {
      fetch("/", { method: 'PUT', body: JSON.stringify({ todos: window.todos }) })
      populateTodos()
    }
    var completeTodo = function(evt) {
      var checkbox = evt.target
      var todoElement = checkbox.parentNode
      var newTodoSet = [].concat(window.todos)
      var todo = newTodoSet.find(t => t.id == todoElement.dataset.todo)
      todo.completed = !todo.completed
      window.todos = newTodoSet
      updateTodos()
    }
    var populateTodos = function() {
      var todoContainer = document.querySelector("#todos")
      todoContainer.innerHTML = null
      window.todos.forEach(todo => {
        var el = document.createElement("div")
        el.className = "border-t py-4"
        el.dataset.todo = todo.id
        var name = document.createElement("span")
        name.className = todo.completed ? "line-through" : ""
        name.textContent = todo.name
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
    populateTodos()
    var createTodo = function() {
      var input = document.querySelector("input[name=name]")
      if (input.value.length) {
        window.todos = [].concat(todos, { id: window.todos.length + 1, name: input.value, completed: false })
        input.value = ""
        updateTodos()
      }
    }
    document.querySelector("#create").addEventListener('click', createTodo)
  </script>
</html>`

const url = Deno.env.get('endpoint');
const defaultData = { todos: [] }
async function getTodos(data) {
  if (typeof data !== 'undefined'){
  const string = JSON.stringify(data);
  const string2 = string.slice(1,-1);
  const newtodo = `{"todos":[${string2}]}`
   var newdata = JSON.parse(newtodo);
}else{
   var newdata = defaultData
}

  const body = html(JSON.stringify(newdata.todos || []).replace(/</g, "\\u003c"))


  return new Response(body, {
    headers: { 'Content-Type': 'text/html' },
  })
}


async function gatherResponse(response) {
  const { headers } = response;
  const contentType = headers.get('content-type') || '';
  if (contentType.includes('application/json')) {
    const data =  await response.json();
    if(data[0]["data"] == undefined){
      return defaultData;
    }else{
      return data[0];
    }
  } else if (contentType.includes('application/text')) {
    return response.text();
  } else if (contentType.includes('text/html')) {
    return response.text();
  } else {
    return response.text();
  }
}

async function handler(request) {
    if (request.method === 'PUT') {
    const ipa = await getIP({ipv6: true});
    const ip = ipa.replaceAll('.', '');  
    const todos = await request.text()
    const putData = `[{"delete":{"objfilter":"SELECT $o:cftodos.${ip}.attrset('delete')"}},{"purge":{}},{"modify":{"data":{"o:cftodos":{"${ip}": ${todos}}}}},{"query":{"sfsql":"SELECT $i:.${ip}.todos.id as id, $s:.${ip}.todos.name as name, $b:.${ip}.todos.completed as completed"}}]`
    const body = putData
    const init = {
      body: body,
      method: 'POST',
      headers: {
        'content-type': 'application/json;charset=UTF-8',
        'x-sfsql-apikey': Deno.env.get('api_key')
      },
    };
    const response = await fetch(url, init);
    return  await getTodos(await response.json());
  } else {
    const ipa = await getIP({ipv6: true});
    const ip = ipa.replaceAll('.', ''); 
    const QData = `[{"query":{"sfsql":"SELECT $i:.${ip}.todos.id as id, $s:.${ip}.todos.name as name, $b:.${ip}.todos.completed as completed"}}]`
    const body = QData
    const init = {
      body: body,
      method: 'POST',
      headers: {
        'content-type': 'application/json;charset=UTF-8',
        'x-sfsql-apikey': Deno.env.get('api_key')
      },
    };
    const response = await fetch(url, init);
    const results = await gatherResponse(response);
    return getTodos(results["data"])

  }
}


console.log("Listening on http://localhost:8000");
serve(handler);