<template>
  <div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold">Pedidos de Exames</h1>
      <button
        @click="createExamRequest"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700"
      >
        Novo Pedido
      </button>
    </div>

    <div v-if="loading" class="text-gray-600">Carregando...</div>

    <div v-else>
      <div
        v-for="request in examRequests"
        :key="request.id"
        class="bg-white border border-gray-200 rounded-lg shadow mb-4 p-4"
      >
        <div class="flex justify-between items-center mb-3">
          <div>
            <h2 class="text-lg font-semibold">Pedido #{{ request.id }}</h2>
            <p class="text-sm text-gray-500">Criado em: {{ formatDate(request.created_at) }}</p>
          </div>
          <div class="space-x-2">
            <button
              @click="goToDetails(request.id)"
              class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700"
            >
              Detalhar
            </button>
            <button
              @click="printRequest(request.id)"
              class="bg-purple-600 text-white px-3 py-1 rounded hover:bg-purple-700"
            >
              Imprimir
            </button>
          </div>
        </div>

        <!-- TABELA DE EXAMES -->
        <div class="overflow-x-auto">
          <table class="w-full table-auto border-collapse">
            <thead>
              <tr class="bg-gray-100">
                <th class="px-3 py-2 border">Exame</th>
                <th class="px-3 py-2 border">Comentário</th>
                <th class="px-3 py-2 border">Pacote</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in request.items" :key="item.id + '-' + (item.package_id || 'avulso')">
                <td class="px-3 py-2 border">{{ item.exam.name }}</td>
                <td class="px-3 py-2 border">{{ item.comment }}</td>
                <td class="px-3 py-2 border">
                  <span v-if="item.package_id">Pacote {{ item.package_id }}</span>
                  <span v-else>Avulso</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- PAGINAÇÃO -->
      <div class="flex justify-center mt-4 space-x-2">
        <button
          @click="changePage(pagination.current_page - 1)"
          :disabled="!pagination.prev_page_url"
          class="px-3 py-1 border rounded disabled:opacity-50"
        >
          Anterior
        </button>

        <button
          v-for="page in displayedPages"
          :key="page"
          @click="changePage(page)"
          :class="['px-3 py-1 border rounded', page === pagination.current_page ? 'bg-blue-600 text-white' : '']"
        >
          {{ page }}
        </button>

        <button
          @click="changePage(pagination.current_page + 1)"
          :disabled="!pagination.next_page_url"
          class="px-3 py-1 border rounded disabled:opacity-50"
        >
          Próximo
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { listExamRequests } from "@/services/examRequests";

export default {
  data() {
    return {
      examRequests: [],
      loading: true,
      pagination: {
        current_page: 1,
        last_page: 1,
        next_page_url: null,
        prev_page_url: null,
      },
    };
  },
  computed: {
    displayedPages() {
      const total = this.pagination.last_page;
      const current = this.pagination.current_page;
      const pages = [];

      let start = current - 2;
      let end = current + 2;

      if (start < 1) {
        end += 1 - start;
        start = 1;
      }
      if (end > total) {
        start -= end - total;
        end = total;
      }
      if (start < 1) start = 1;

      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      return pages;
    },
  },
  methods: {
    async loadData(page = 1) {
      this.loading = true;
      try {
        const response = await listExamRequests(page);
        this.examRequests = response.data;
        this.pagination = {
          current_page: response.current_page,
          last_page: response.last_page,
          next_page_url: response.next_page_url,
          prev_page_url: response.prev_page_url,
        };
      } catch (e) {
        console.error("Erro ao carregar exam-requests", e);
      } finally {
        this.loading = false;
      }
    },
    changePage(page) {
      if (page < 1 || page > this.pagination.last_page) return;
      this.loadData(page);
    },
    goToDetails(id) {
      this.$router.push({ name: "ExamRequest", params: { id } });
    },
    createExamRequest() {
      this.$router.push({ name: "ExamRequestCreate" });
    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleString();
    },
    printRequest(id) {
      window.open(`/api/exam-requests/${id}/print`, "_blank");
    },
  },
  mounted() {
    this.loadData();
  },
};
</script>

<style scoped>
</style>
