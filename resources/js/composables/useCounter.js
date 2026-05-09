import { ref } from 'vue'

// Función composable - debe empezar con "use"
export function useCounter() {
    // Crear dato reactivo
    const count = ref(0)
    
    // Función para incrementar
    function increment() {
        count.value++
    }
    
    // Función para decrementar
    function decrement() {
        count.value--
    }
    
    // Retornar dato y funciones
    return {
        count,
        increment,
        decrement
    }
}
