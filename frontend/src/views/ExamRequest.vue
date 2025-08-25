<template>
  <div class="p-6 space-y-6">
    <!-- Cabeçalho -->
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold">Detalhes do Pedido de Exames</h1>
      <div class="space-x-2">
        <button
          class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700"
          @click="$router.push('/')"
        >
          Voltar
        </button>
        <button
          class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700"
          @click="printRequest"
        >
          Imprimir
        </button>
      </div>
    </div>

    <!-- Exames Avulsos -->
    <div v-if="avulsos.length" class="bg-white border border-gray-200 rounded-lg shadow p-4">
      <h2 class="font-semibold text-lg mb-3">Exames Avulsos</h2>
      <div class="overflow-x-auto">
        <table class="w-full table-auto border-collapse">
          <thead>
            <tr class="bg-gray-100">
              <th class="px-3 py-2 border">Exame</th>
              <th class="px-3 py-2 border">Lateralidade</th>
              <th class="px-3 py-2 border">Comentário</th>
              <th class="px-3 py-2 border">Grupo</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="exam in avulsos" :key="exam.id">
              <td class="px-3 py-2 border font-medium">{{ exam.exam.name }}</td>
              <td class="px-3 py-2 border">{{ exam.laterality || exam.exam.laterality || '-' }}</td>
              <td class="px-3 py-2 border text-gray-600">{{ exam.comment || '-' }}</td>
              <td class="px-3 py-2 border">{{ exam.group || 'Individual' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="avulsosObservation" class="mt-2 px-3 py-2 bg-gray-50 text-gray-700 text-sm rounded">
        <strong>Observação:</strong> {{ avulsosObservation }}
      </div>
    </div>

    <!-- Pacotes -->
    <div
      v-for="pkg in packages"
      :key="pkg.id"
      class="bg-white border border-gray-200 rounded-lg shadow p-4"
    >
      <div class="flex justify-between items-center mb-3">
        <h2 class="font-semibold text-lg">{{ pkg.name }}</h2>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full table-auto border-collapse">
          <thead>
            <tr class="bg-gray-100">
              <th class="px-3 py-2 border">Exame</th>
              <th class="px-3 py-2 border">Lateralidade</th>
              <th class="px-3 py-2 border">Comentário</th>
              <th class="px-3 py-2 border">Grupo</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="exam in pkg.exams" :key="exam.id">
              <td class="px-3 py-2 border font-medium">{{ exam.exam.name }}</td>
              <td class="px-3 py-2 border">{{ exam.laterality || exam.exam.laterality || '-' }}</td>
              <td class="px-3 py-2 border text-gray-600">{{ exam.comment || '-' }}</td>
              <td class="px-3 py-2 border">{{ exam.group || 'Individual' }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="pkg.observation" class="mt-2 px-3 py-2 bg-gray-50 text-gray-700 text-sm rounded">
        <strong>Observação:</strong> {{ pkg.observation }}
      </div>
    </div>
  </div>
</template>

<script>
import { getExamRequest } from '@/services/examRequests'

export default {
  data() {
    return {
      avulsos: [],
      avulsosObservation: '',
      packages: []
    }
  },
  methods: {
    async fetchExamRequest() {
      try {
        const data = await getExamRequest(this.$route.params.id)

        // Separa exames avulsos e por pacote
        const avulsoList = data.items.filter(i => i.package_id === null)
        const packageMap = {}

        data.items
          .filter(i => i.package_id !== null)
          .forEach(i => {
            const pkg = i.package
            if (!packageMap[pkg.id]) {
              packageMap[pkg.id] = {
                id: pkg.id,
                name: pkg.name,
                observation: pkg.observations,
                exams: []
              }
            }
            packageMap[pkg.id].exams.push(i)
          })

        this.avulsos = avulsoList
        this.packages = Object.values(packageMap)
        this.avulsosObservation = ''
      } catch (err) {
        console.error('Erro ao buscar pedido de exame:', err)
      }
    },
    printRequest() {
      const url = `/api/exam-requests/${this.$route.params.id}/print`
      window.open(url, '_blank')
    }
  },
  mounted() {
    this.fetchExamRequest()
  }
}
</script>

<style scoped>
/* Todo estilo é via Tailwind */
</style>
