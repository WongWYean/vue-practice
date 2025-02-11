<script setup lang="ts">
import { onMounted, ref } from 'vue'

const name = ref('John Doe')
const status = ref('active')
const tasks = ref([
  { name: 'Task 1', completed: true },
  { name: 'Task 2', completed: false },
  { name: 'Task 3', completed: true },
])
const newTask = ref('')
const link = 'https://www.google.com'

const toggleStatus = () => {
  if (status.value === 'active') {
    status.value = 'pending'
  } else if (status.value === 'pending') {
    status.value = 'inactive'
  } else {
    status.value = 'active'
  }
}

const addTask = () => {
  if (newTask.value.trim() === '') return
  tasks.value.push({ name: newTask.value, completed: false })
  newTask.value = ''
}

const deleteTask = (index: number) => {
  tasks.value.splice(index, 1)
}

onMounted(async () => {
  try {
    const response = await fetch('https://jsonplaceholder.typicode.com/todos')
    const data = await response.json()
    tasks.value = data.map((task: any) => ({
      name: task.title,
      completed: task.completed,
    }))
  } catch (error) {
    console.error('Error fetching data', error)
  }
  console.log('Component mounted')
})
</script>

<template>
  <div>
    <h1>{{ name }}</h1>
    <p v-if="status === 'active'">User is active</p>
    <p v-else-if="status === 'pending'">User is pending</p>
    <p v-else>User is not active</p>
    <button @click="toggleStatus">Change status</button>
  </div>

  <div>
    <form @submit.prevent="addTask">
      <label for="newTask">Add Task</label>
      <input type="text" id="newTask" name="newTask" v-model="newTask" />
      <button type="submit">Submit</button>
    </form>

    <h3>Tasks</h3>
    <ul>
      <li v-for="(task, index) in tasks" :key="task.name">
        <span v-if="task.completed">✅</span>
        <span v-else>❌</span>
        {{ task.name }}
        <button @click="deleteTask(index)">Delete</button>
        <button @click="task.completed = !task.completed">
          {{ task.completed ? 'Mark as incomplete' : 'Mark as complete' }}
        </button>
      </li>
    </ul>
  </div>

  <a :href="link">Click here for Google</a>
</template>
