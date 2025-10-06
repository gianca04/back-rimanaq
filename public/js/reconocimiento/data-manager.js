// === FUNCIONES DE EXPORTACIÓN E IMPORTACIÓN ===

class DataManager {
  constructor(gestureSystem) {
    this.gestureSystem = gestureSystem;
  }

  exportGesture(gestureIndex) {
    if (this.gestureSystem.savedGestures.length === 0) {
      alert("No hay gestos para exportar.");
      return;
    }

    if (gestureIndex < 0 || gestureIndex >= this.gestureSystem.savedGestures.length) {
      alert("Gesto no válido para exportar.");
      return;
    }

    const gesture = this.gestureSystem.savedGestures[gestureIndex];
    
    const gestureData = {
      version: "1.0",
      createdAt: new Date().toISOString(),
      gesture: gesture,
    };

    const blob = new Blob([JSON.stringify(gestureData, null, 2)], {
      type: "application/json",
    });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `gesto_${gesture.name}_${
      new Date().toISOString().split("T")[0]
    }.json`;
    a.click();
    URL.revokeObjectURL(url);

    this.gestureSystem.statusText.textContent = `Gesto "${gesture.name}" exportado`;
  }

  exportAllGestures() {
    if (this.gestureSystem.savedGestures.length === 0) {
      alert("No hay gestos para exportar.");
      return;
    }

    this.gestureSystem.savedGestures.forEach((gesture, index) => {
      setTimeout(() => {
        this.exportGesture(index);
      }, index * 100); // Pequeño delay para evitar problemas con múltiples descargas
    });

    this.gestureSystem.statusText.textContent = `${this.gestureSystem.savedGestures.length} gestos exportados individualmente`;
  }

  importGesture(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
      try {
        const data = JSON.parse(e.target.result);

        // Validar si es un gesto individual o un dataset
        let gestureToImport = null;
        
        if (data.gesture) {
          // Formato individual
          gestureToImport = data.gesture;
        } else if (data.gestures && Array.isArray(data.gestures) && data.gestures.length === 1) {
          // Dataset con un solo gesto
          gestureToImport = data.gestures[0];
        } else if (data.gestures && Array.isArray(data.gestures)) {
          // Dataset múltiple - preguntar si quiere importar todos
          const importAll = confirm(
            `Este archivo contiene ${data.gestures.length} gestos. ¿Quieres importar todos? (Cancelar para importar solo el primero)`
          );
          
          if (importAll) {
            return this.importMultipleGestures(data.gestures);
          } else {
            gestureToImport = data.gestures[0];
          }
        } else {
          alert("Formato de archivo inválido. El archivo debe contener un gesto válido.");
          return;
        }

        if (gestureToImport) {
          // Verificar si ya existe un gesto con el mismo nombre
          const existingIndex = this.gestureSystem.savedGestures.findIndex(
            (g) => g.name === gestureToImport.name
          );
          
          if (existingIndex !== -1) {
            const replace = confirm(
              `Ya existe un gesto llamado "${gestureToImport.name}". ¿Quieres reemplazarlo?`
            );
            
            if (replace) {
              this.gestureSystem.savedGestures[existingIndex] = gestureToImport;
            } else {
              return;
            }
          } else {
            this.gestureSystem.savedGestures.push(gestureToImport);
          }

          this.saveSavedGestures();
          this.gestureSystem.uiManager.updateDisplay();
          this.gestureSystem.statusText.textContent = `Gesto "${gestureToImport.name}" importado`;
        }
      } catch (error) {
        alert("Error al leer el archivo. Asegúrate de que sea un archivo JSON válido.");
        console.error("Error importing gesture:", error);
      }
    };
    reader.readAsText(file);

    // Limpiar input para permitir importar el mismo archivo nuevamente
    event.target.value = "";
  }

  importMultipleGestures(gestures) {
    const replace = confirm(
      "¿Quieres reemplazar los gestos actuales? (Cancelar para agregar a los gestos existentes)"
    );

    if (replace) {
      this.gestureSystem.savedGestures = gestures;
    } else {
      // Agregar gestos nuevos, evitando duplicados por nombre
      gestures.forEach((newGesture) => {
        const existingIndex = this.gestureSystem.savedGestures.findIndex(
          (g) => g.name === newGesture.name
        );
        if (existingIndex !== -1) {
          // Reemplazar gesto existente
          this.gestureSystem.savedGestures[existingIndex] = newGesture;
        } else {
          // Agregar nuevo gesto
          this.gestureSystem.savedGestures.push(newGesture);
        }
      });
    }

    this.saveSavedGestures();
    this.gestureSystem.uiManager.updateDisplay();
    this.gestureSystem.statusText.textContent = `${gestures.length} gestos importados`;
  }

  loadSavedGestures() {
    const saved = localStorage.getItem("savedGestures");
    return saved ? JSON.parse(saved) : [];
  }

  saveSavedGestures() {
    localStorage.setItem("savedGestures", JSON.stringify(this.gestureSystem.savedGestures));
  }
}
