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
    var updateTodos = function(updatetodo) {
      fetch("/", { method: 'PUT', body: JSON.stringify({ todos: updatetodo }) })
      populateTodos()
    }
    var completeTodo = function(evt) {
      var checkbox = evt.target
      var todoElement = checkbox.parentNode
      var newTodoSet = [].concat(window.todos)
      var todo = newTodoSet.find(t => t.id == todoElement.dataset.todo)
      todo.completed = !todo.completed
      window.todos = newTodoSet
      updateTodos(todo)
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
        todo = { id: window.todos.length + 1, name: input.value, completed: false }
        input.value = ""
        updateTodos(todo)
      }
    }
    document.querySelector("#create").addEventListener('click', createTodo)
  </script>
</html>`

const url = endpoint;
const defaultData = { todos: [] }

async function getTodos(data) {
  if (typeof data !== 'undefined'){
  const string = JSON.stringify(data);
  const string2 = string.slice(1,-1);
  const newtodo = `{"todos":[${string2}]}`
   newdata=JSON.parse(newtodo);
}else{
   newdata = defaultData
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

async function handleRequest(request) {
    if (request.method === 'PUT') {
    const ipa = request.headers.get('CF-Connecting-IP')
    const ip = ipa.replaceAll('.', '').replaceAll(':', '');
    const todos = await request.text()
    const updatedtodo = JSON.parse(todos);
    const id = updatedtodo.todos.id;
    const todostring=JSON.stringify(updatedtodo.todos);
    const putData = `[{"modify":{"data":{"o:cftodos":{"${ip}":{"todos":[{"#set":{"where":"$i:todos.id=${id}"}},${todostring}]}}}}}]`
    const body = putData
    const init = {
      body: body,
      method: 'POST',
      headers: {
        'content-type': 'application/json;charset=UTF-8',
        'x-sfsql-apikey': api_key,
      },
    };
    const response = await fetch(url, init);
    return new Response(body, { status: 200 })
  } else {
    const ipa = request.headers.get('CF-Connecting-IP');
    const ip = ipa.replaceAll('.', '').replaceAll(':', '');
    const QData = `[{"query":{"sfsql":"SELECT $i:.${ip}.todos.id as id, $s:.${ip}.todos.name as name, $b:.${ip}.todos.completed as completed"}}]`
    const body = QData
    const init = {
      body: body,
      method: 'POST',
      headers: {
        'content-type': 'application/json;charset=UTF-8',
        'x-sfsql-apikey': api_key,
      },
    };
    const response = await fetch(url, init);
    const results = await gatherResponse(response);
    return getTodos(results["data"])

  }
}

addEventListener('fetch', event => {
  event.respondWith(handleRequest(event.request));
});