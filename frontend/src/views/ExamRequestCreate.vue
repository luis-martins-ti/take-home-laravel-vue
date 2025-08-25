<template>
  <div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold">Novo Pedido de Exames</h1>
      <button
        @click="saveRequest"
        class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700"
      >
        Salvar Pedido
      </button>
    </div>

    <!-- Exames Avulsos -->
    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow">
      <div class="flex justify-between items-center mb-3">
        <h2 class="font-semibold text-lg">Exames Avulsos</h2>
      </div>

      <div class="flex items-center space-x-2 mb-2 w-full">
        <!-- Input autocomplete -->
        <div class="relative flex-1">
          <input
            v-model="newExam.search"
            @input="searchExams"
            type="text"
            placeholder="Buscar exame..."
            :class="['border rounded px-2 py-1 w-full', isDuplicateAvulso ? 'border-red-500' : 'border-gray-300']"
          />
          <ul
            v-if="examSuggestions.length"
            class="absolute top-full left-0 bg-white border border-gray-200 rounded mt-1 shadow max-h-40 overflow-y-auto z-10 w-full"
          >
            <li
              v-for="exam in examSuggestions"
              :key="exam.id"
              @click="selectExam(exam)"
              class="px-3 py-1 hover:bg-gray-100 cursor-pointer"
            >
              {{ exam.name }} ({{ exam.laterality }})
            </li>
          </ul>
        </div>

        <select v-model="newExam.laterality" class="border border-gray-300 rounded px-2 py-1">
          <option value="OD">Olho Direito</option>
          <option value="OE">Olho Esquerdo</option>
          <option value="AO">Ambos os Olhos</option>
        </select>
        <input
          v-model="newExam.comment"
          placeholder="Comentário"
          class="border border-gray-300 rounded px-2 py-1 flex-1"
        />
        <select v-model="newExam.group" class="border border-gray-300 rounded px-2 py-1">
          <option value="Individual">Individual</option>
          <option v-for="n in 5" :key="n" :value="'Grupo ' + n">Grupo {{ n }}</option>
        </select>
        <button
          @click="addExamAvulso"
          class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700"
          :disabled="isDuplicateAvulso || !newExam.exam_id"
        >
          Adicionar
        </button>
      </div>

      <!-- Lista de exames avulsos -->
      <div class="overflow-x-auto">
        <table class="w-full table-auto border-collapse">
          <thead>
            <tr class="bg-gray-100">
              <th class="px-3 py-2 border">Exame</th>
              <th class="px-3 py-2 border">Lateralidade</th>
              <th class="px-3 py-2 border">Comentário</th>
              <th class="px-3 py-2 border">Grupo de impressão</th>
              <th class="px-3 py-2 border">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in examItems.filter(i => i.groupType === 'Individual')"
              :key="item.exam_id + '-' + item.group"
            >
              <td class="px-3 py-2 border">{{ item.name }}</td>
              <td class="px-3 py-2 border">
                <select v-model="item.laterality" class="border border-gray-300 rounded px-2 py-1">
                  <option value="OD">Olho Direito</option>
                  <option value="OE">Olho Esquerdo</option>
                  <option value="AO">Ambos os Olhos</option>
                </select>
              </td>
              <td class="px-3 py-2 border">
                <input v-model="item.comment" placeholder="Comentário" class="border border-gray-300 rounded px-2 py-1 w-full" />
              </td>
              <td class="px-3 py-2 border">
                <select v-model="item.group" class="border border-gray-300 rounded px-2 py-1">
                  <option value="Individual">Individual</option>
                  <option v-for="n in 5" :key="n" :value="'Grupo ' + n">Grupo {{ n }}</option>
                </select>
              </td>
              <td class="px-3 py-2 border text-center">
                <button @click="removeItem(item)" class="text-red-500 hover:text-red-700">✕</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pacotes -->
    <div class="space-y-4">
      <div
        v-for="pkg in packages"
        :key="pkg.id"
        class="bg-gray-50 border border-gray-200 rounded-lg p-3"
      >
        <div class="flex justify-between items-center mb-2">
          <h3 class="font-semibold">{{ pkg.name }}</h3>
          <button
            @click="removePackage(pkg)"
            class="text-red-500 hover:text-red-700 text-sm"
          >
            Remover pacote
          </button>
        </div>

        <table class="w-full table-auto border-collapse">
          <thead>
            <tr class="bg-gray-100">
              <th class="px-3 py-2 border">Exame</th>
              <th class="px-3 py-2 border">Lateralidade</th>
              <th class="px-3 py-2 border">Comentário</th>
              <th class="px-3 py-2 border">Grupo de impressão</th>
              <th class="px-3 py-2 border">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in pkg.items"
              :key="item.exam_id + '-' + item.package_id"
            >
              <td class="px-3 py-2 border">{{ item.name }}</td>
              <td class="px-3 py-2 border">
                <select v-model="item.laterality" class="border border-gray-300 rounded px-2 py-1">
                  <option value="OD">Olho Direito</option>
                  <option value="OE">Olho Esquerdo</option>
                  <option value="AO">Ambos os Olhos</option>
                </select>
              </td>
              <td class="px-3 py-2 border">
                <input v-model="item.comment" placeholder="Comentário" class="border border-gray-300 rounded px-2 py-1 w-full" />
              </td>
              <td class="px-3 py-2 border">
                <select v-model="item.group" class="border border-gray-300 rounded px-2 py-1">
                  <option value="Individual">Individual</option>
                  <option v-for="n in 5" :key="n" :value="'Grupo ' + n">Grupo {{ n }}</option>
                </select>
              </td>
              <td class="px-3 py-2 border text-center">
                <button @click="removeExamFromPackage(pkg, item)" class="text-red-500 hover:text-red-700">✕</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Botão abrir modal pacotes -->
    <div>
      <button
        @click="showPackageModal = true"
        class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700"
      >
        Adicionar Pacote
      </button>
    </div>

    <!-- Modal pacotes -->
    <div
      v-if="showPackageModal"
      class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg p-6 w-96">
        <h2 class="text-lg font-bold mb-4">Selecionar Pacote</h2>
        <input v-model="packageSearch" placeholder="Buscar pacote..." class="border rounded px-2 py-1 mb-2 w-full" />
        <ul class="max-h-60 overflow-y-auto">
          <li
            v-for="pkg in filteredPackages"
            :key="pkg.id"
            class="flex justify-between items-center mb-2"
          >
            <span>{{ pkg.name }}</span>
            <button
              @click="addPackage(pkg)"
              class="bg-purple-600 text-white px-2 py-1 rounded hover:bg-purple-700 text-sm"
            >
              Adicionar
            </button>
          </li>
        </ul>
        <button
          @click="showPackageModal = false"
          class="mt-4 bg-gray-300 px-3 py-1 rounded hover:bg-gray-400"
        >
          Fechar
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { createExamRequest } from "@/services/examRequests"
import { listPackages } from "@/services/packages"
import { searchExams as searchExamsApi } from "@/services/exams"
import debounce from "lodash/debounce"

export default {
  data() {
    return {
      examItems: [],
      newExam: { search: "", exam_id: null, name: "", laterality: "AO", comment: "", group: "Individual" },
      examSuggestions: [],
      showPackageModal: false,
      packages: [],
      availablePackages: [],
      packageSearch: "",
    }
  },
  computed: {
    isDuplicateAvulso() {
      if (!this.newExam.exam_id) return false
      return this.examItems.some(i => i.exam_id === this.newExam.exam_id && i.package_id)
    },
    filteredPackages() {
      if (!this.packageSearch) return this.availablePackages
      return this.availablePackages.filter(p => p.name.toLowerCase().includes(this.packageSearch.toLowerCase()))
    }
  },
  methods: {
    async fetchPackages() {
      try {
        const data = await listPackages()
        this.availablePackages = data.map(pkg => ({
          id: pkg.id,
          name: pkg.name,
          items: pkg.exams.map(exam => ({
            exam_id: exam.id,
            name: exam.name,
            laterality: exam.laterality || "AO",
            comment: "",
            group: "Individual",
            groupType: "Package",
            package_id: pkg.id
          }))
        }))
      } catch (e) {
        console.error("Erro ao carregar pacotes", e)
      }
    },
    searchExams: debounce(async function() {
      if (this.newExam.search.length < 3) {
        this.examSuggestions = []
        return
      }
      try {
        this.examSuggestions = await searchExamsApi(this.newExam.search)
      } catch (e) {
        console.error("Erro ao buscar exames", e)
      }
    }, 300),
    selectExam(exam) {
      this.newExam.exam_id = exam.id
      this.newExam.name = exam.name
      this.newExam.search = exam.name
      this.newExam.laterality = exam.laterality || "AO"
      this.examSuggestions = []
    },
    addExamAvulso() {
      if (!this.newExam.exam_id || this.isDuplicateAvulso) return
      this.examItems.push({ 
        exam_id: this.newExam.exam_id,
        name: this.newExam.name,
        laterality: this.newExam.laterality,
        comment: this.newExam.comment,
        group: this.newExam.group,
        groupType: "Individual"
      })
      this.newExam = { search: "", exam_id: null, name: "", laterality: "AO", comment: "", group: "Individual" }
    },
    removeItem(item) {
      this.examItems = this.examItems.filter(i => i !== item)
    },
    addPackage(pkg) {
      if (this.packages.some(p => p.id === pkg.id)) return
      this.packages.push(pkg)
      this.examItems.push(...pkg.items)
      this.showPackageModal = false
    },
    removePackage(pkg) {
      this.packages = this.packages.filter(p => p.id !== pkg.id)
      this.examItems = this.examItems.filter(i => i.package_id !== pkg.id)
    },
    removeExamFromPackage(pkg, exam) {
      pkg.items = pkg.items.filter(i => i.exam_id !== exam.exam_id)
      this.examItems = this.examItems.filter(i => !(i.package_id === pkg.id && i.exam_id === exam.exam_id))
    },
    async saveRequest() {
      try {
        const payload = {
          items: this.examItems.map(i => ({
            exam_id: i.exam_id,
            laterality: i.laterality,
            comment: i.comment,
            group: i.group,
            package_id: i.package_id || null
          }))
        }

        const createdRequest = await createExamRequest(payload)

        alert("Pedido criado com sucesso!")
        
        // Redireciona para o detalhar pedido usando o ID retornado
        this.$router.push({ name: "ExamRequest", params: { id: createdRequest.id } })
        
      } catch (e) {
        console.error(e)
        alert("Erro ao criar pedido")
      }
    }
  },
  mounted() {
    this.fetchPackages()
  }
}
</script>

<style scoped>
/* separação entre seções */
</style>
