/**
 * Classe ApiService para facilitar chamadas HTTP (GET, POST, PUT, DELETE) a APIs RESTful.
 * Suporta autenticação via token Bearer e fornece métodos reutilizáveis para diferentes tipos de requisições.
 */
export default class ApiService {
    #token;  // Atributo privado para armazenar o token de autenticação

    /**
     * Construtor da classe ApiService.
     * @param {string|null} token - Token de autenticação opcional para incluir no header Authorization.
     */
    constructor(token = null) {
        this.#token = token;
    }

    /**
     * Método interno para lidar com a resposta HTTP
     * @private
     */
    async #handleResponse(response) {
        const text = await response.text();
        
        // Verifica se é JSON
        try {
            const data = text ? JSON.parse(text) : null;
            
            if (!response.ok) {
                const error = new Error(data?.message || data?.error?.message || text || `HTTP error! status: ${response.status}`);
                error.status = response.status;
                error.data = data;
                throw error;
            }
            
            return data;
        } catch (error) {
            if (error instanceof SyntaxError) {
                // Não é JSON, retorna texto
                if (!response.ok) {
                    throw new Error(text || `HTTP error! status: ${response.status}`);
                }
                return text;
            }
            throw error;
        }
    }

    /**
     * Método para fazer uma requisição GET simples sem headers adicionais.
     * Útil para APIs públicas que não requerem autenticação.
     * @param {string} uri - URL do recurso para a requisição GET.
     * @returns {Promise<Object|Array>} Retorna o JSON obtido da resposta ou array vazio em caso de erro.
     */
    async simpleGet(uri) {
        try {
            const response = await fetch(uri);
            return await this.#handleResponse(response);
        } catch (error) {
            console.error("Erro ao buscar dados:", error.message);
            return { success: false, message: error.message };
        }
    }

    /**
     * Método para requisição GET com headers, incluindo token se presente.
     * Usado para APIs que exigem autenticação ou headers customizados.
     * @param {string} uri - URL do recurso para a requisição GET.
     * @returns {Promise<Object|Array>} Retorna JSON da resposta ou array vazio em caso de erro.
     */
    async get(uri) {
        try {
            const headers = {
                "Content-Type": "application/json"
            };

            if (this.#token) {
                headers["Authorization"] = `Bearer ${this.#token}`;
            }

            const response = await fetch(uri, {
                method: "GET",
                headers: headers
            });

            return await this.#handleResponse(response);
        } catch (error) {
            console.error("Erro ao buscar dados:", error.message);
            return { 
                success: false, 
                message: error.message,
                status: error.status
            };
        }
    }

    /**
     * Método para buscar um recurso específico pelo ID via GET.
     * Monta a URL com o ID no final e faz a requisição.
     * @param {string} uri - URL base do recurso.
     * @param {string|number} id - Identificador do recurso a ser buscado.
     * @returns {Promise<Object|null>} Retorna JSON do recurso ou null em caso de erro.
     */
    async getById(uri, id) {
        try {
            const headers = {
                "Content-Type": "application/json"
            };

            if (this.#token) {
                headers["Authorization"] = `Bearer ${this.#token}`;
            }

            const fullUri = `${uri}/${id}`;
            const response = await fetch(fullUri, {
                method: "GET",
                headers: headers
            });

            return await this.#handleResponse(response);
        } catch (error) {
            console.error("Erro ao buscar por ID:", error.message);
            return { success: false, message: error.message };
        }
    }

    /**
     * Método para enviar dados via POST para criar um novo recurso.
     * Envia o objeto JSON serializado no corpo da requisição.
     * @param {string} uri - URL do endpoint para POST.
     * @param {Object} jsonObject - Objeto a ser enviado como corpo JSON.
     * @returns {Promise<Object|Array>} Retorna JSON da resposta ou array vazio em caso de erro.
     */
    async post(uri, jsonObject) {
        try {
            const headers = {
                "Content-Type": "application/json"
            };

            if (this.#token) {
                headers["Authorization"] = `Bearer ${this.#token}`;
            }

            const response = await fetch(uri, {
                method: "POST",
                headers: headers,
                body: JSON.stringify(jsonObject)
            });

            return await this.#handleResponse(response);
        } catch (error) {
            console.error("Erro ao enviar dados:", error.message);
            return { success: false, message: error.message };
        }
    }

    /**
     * Método para atualizar um recurso via PUT usando ID e objeto JSON.
     * @param {string} uri - URL base do recurso.
     * @param {string|number} id - ID do recurso a ser atualizado.
     * @param {Object} jsonObject - Dados atualizados a serem enviados no corpo da requisição.
     * @returns {Promise<Object|null>} Retorna JSON da resposta ou null em caso de erro.
     */
    async put(uri, id, jsonObject) {
        try {
            const headers = {
                "Content-Type": "application/json"
            };

            if (this.#token) {
                headers["Authorization"] = `Bearer ${this.#token}`;
            }

            const fullUri = `${uri}/${id}`;
            const response = await fetch(fullUri, {
                method: "PUT",
                headers: headers,
                body: JSON.stringify(jsonObject)
            });

            return await this.#handleResponse(response);
        } catch (error) {
            console.error("Erro ao atualizar dados:", error.message);
            return { success: false, message: error.message };
        }
    }

    /**
     * Método para deletar um recurso via DELETE usando ID.
     * @param {string} uri - URL base do recurso.
     * @param {string|number} id - ID do recurso a ser deletado.
     * @returns {Promise<Object|null>} Retorna JSON da resposta ou null se não houver corpo ou erro.
     */
    async delete(uri, id) {
        try {
            const headers = {
                "Content-Type": "application/json"
            };

            if (this.#token) {
                headers["Authorization"] = `Bearer ${this.#token}`;
            }

            const fullUri = `${uri}/${id}`;
            const response = await fetch(fullUri, {
                method: "DELETE",
                headers: headers
            });

            return await this.#handleResponse(response);
        } catch (error) {
            console.error("Erro ao deletar dados:", error.message);
            return { success: false, message: error.message };
        }
    }

    /**
     * Getter para o token privado.
     * @returns {string|null} Retorna o token atual.
     */
    get token() {
        return this.#token;
    }

    /**
     * Setter para atualizar o token privado.
     * @param {string} value - Novo token a ser setado.
     */
    set token(value) {
        this.#token = value;
    }
}