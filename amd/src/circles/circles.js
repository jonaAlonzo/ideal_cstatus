function create_circle(ids_numbers, count_cabecera, name_container_principal_1, cabecera0, name_container_padre, name_container_centro, texts, numberOfCircles, competencia_ok_for_user, ids_circles_cambio_color, cabeceras) {
    try {
        var name_competencia_cambio_color_A_D_L = [];
        // Cabeceras
        var cabecera_h1 = document.getElementById(cabecera0);
        if (count_cabecera > 0) {
            var text_cabecera_h1 = document.createTextNode(cabeceras);
            cabecera_h1.appendChild(text_cabecera_h1);
        }
        // Container principal
        var container_principal = document.getElementById(name_container_principal_1);
        if (!container_principal) return;

        var container_padre = document.createElement('div');
        container_padre.setAttribute("id", name_container_padre);
        container_principal.appendChild(container_padre);

        // if (cabecera0 == 'cabecera_6' || cabecera0 == 'cabecera_7') {
        if (cabecera0 == 'cabecera_6' || cabecera0 == 'cabecera_7' || cabecera0 == 'cabecera_8') {
            Object.assign(container_padre.style, {
                background: "black",
                width: "258px",
                height: "258px",
                borderRadius: "50%",
                position: "relative",
                overflow: "visible",
                display: "flex"
            });
        } else {
            Object.assign(container_padre.style, {
                background: "black",
                width: "150px",
                height: "150px",
                borderRadius: "50%",
                position: "relative",
                overflow: "visible",
                margin: "15%",
                display: "flex"
            });
        }
        // Container centro
        var container_centro = document.createElement('div');
        container_centro.setAttribute("id", name_container_centro);
        container_centro.className = "circle_center_modal";
        container_padre.appendChild(container_centro);

        document.getElementById(name_container_centro).innerHTML = cabecera0.slice(9, 10);

        // if (cabecera0 == 'cabecera_6' || cabecera0 == 'cabecera_7' ) {
        if (cabecera0 == 'cabecera_6' || cabecera0 == 'cabecera_7' || cabecera0 == 'cabecera_8') {
            Object.assign(container_centro.style, {
                background: "white",
                width: "248px",
                height: "248px",
                borderRadius: "50%",
                margin: "0 auto",
                alignSelf: "center",
                textAlign: "center",
                alignContent: "center",
                display: "flex",
                flexWrap: "wrap",
                justifyContent: "space-around",
                fontSize: "2rem",
                fontWeight: 'bold',
            });

            var childRadius = childSize / 4; // Radio del círculo pequeño
        } else {
            Object.assign(container_centro.style, {
                background: "white",
                width: "142px",
                height: "142px",
                borderRadius: "50%",
                margin: "0 auto",
                alignSelf: "center",
                textAlign: "center",
                alignContent: "center",
                display: "flex",
                flexWrap: "wrap",
                justifyContent: "space-around",
                fontSize: "2rem",
                fontWeight: 'bold',
            });
            var childRadius = childSize / 2; 
        }
        var containerRadius = container_padre.offsetWidth / 2; // Radio del círculo grande
        var childSize = 20; // Tamaño de los círculos pequeños (ancho/alto)
        var childRadius = childSize / 2; // Radio del círculo pequeño
        for (var f = 0; f < numberOfCircles; f++) {
            try {
                var div = document.createElement("div");
                div.className = "circles_1";
                var id_circle_peque= texts[f];
                //remplazar . por _
                id_circle_peque= id_circle_peque.replace('.','_')
                //quitar espacios
                id_circle_peque=id_circle_peque.replace(/\s+/g, '');
                id_circle_peque="circle_"+id_circle_peque;
                div.setAttribute("id", id_circle_peque);
                for (var k = 0; k < competencia_ok_for_user.length; k++) {
                    if (competencia_ok_for_user[k].slice(0, 3) === texts[f].slice(0, 3)) {
                        ids_circles_cambio_color.push(texts[f]);
                        name_competencia_cambio_color_A_D_L.push(competencia_ok_for_user[k].slice(0, 4));
                    }
                }
                Object.assign(div.style, {
                    width: numberOfCircles < 5 ? "65px" : "50px",
                    height: numberOfCircles < 5 ? "65px" : "50px",
                    position: "absolute",
                    textAlign: "center",
                    lineHeight: numberOfCircles < 5 ? "65px" : "50px" 
                });

                var txt_div = document.createTextNode(texts[f]);
                div.appendChild(txt_div);
                //Angulo en radianes
                var angle =  (f*2*Math.PI/numberOfCircles) +3 *Math.PI/2 ;
                var x = containerRadius + (containerRadius - childRadius) * Math.cos(angle);
                var y = containerRadius + (containerRadius - childRadius) * Math.sin(angle);
                div.style.top = y + 'px';
                div.style.left = x + 'px';
                container_padre.appendChild(div);
            } catch (error) {
                console.error("Error creating circle element:", error);
            }
        }
        pintar(texts, name_container_centro, ids_numbers, name_competencia_cambio_color_A_D_L, ids_circles_cambio_color);

    } catch (error) {
        console.error("Error in create_circle function:", error);
    }

    function pintar(texts, name_container_centro, ids_numbers, name_competencia_cambio_color_A_D_L, ids_circles_cambio_color) {
        try {
            let levels = { "A": 0, "D": 0, "L": 0, "O": 0 };
            ids_circles_cambio_color.forEach((id, i) => {
                id= id.replace('.','_')
                id="circle_"+id;
                id= id.replace(/\s+/g, '');
                var circle_cambio_color = document.getElementById(id);
                if (circle_cambio_color) {
                    switch (name_competencia_cambio_color_A_D_L[i].slice(3, 4)) {
                        case 'A':
                            circle_cambio_color.style.backgroundColor = "#0066FF";
                            circle_cambio_color.style.color = "white";
                            circle_cambio_color.style.border = "inset black 3px";
                            levels["A"] += 1;
                            break;
                        case 'D':
                            circle_cambio_color.style.backgroundColor = "#FF6600";
                            circle_cambio_color.style.color = "white";
                            levels["D"] += 1;
                            break;
                        case 'L':
                            circle_cambio_color.style.backgroundColor = "#6600FF";
                            circle_cambio_color.style.color = "white";
                            levels["L"] += 1;
                            break;
                        default:
                            circle_cambio_color.style.backgroundColor = "#C000FF";
                            circle_cambio_color.style.color = "white";
                            levels["O"] += 1;
                            break;
                    }
                }
            });
            comprobar_color_circles_peque(texts, levels, name_container_centro);

        } catch (error) {
            console.error("Error in pintar function:", error);
        }
    }
};

function create_sub_area(title){
    var subarea_class= title.slice(0,3).replace('.','_');
    // console.log(subarea_class);

    var subarea=document.createElement("div");

    subarea.innerHTML=title;
    subarea.setAttribute("class","sub_area red");
   

    return subarea;
}

window.comprobar_color_circles_peque = function (texts, levels, name_container_centro) {
    try {
        // Obtener el elemento
        var circle_centro_div_white = document.getElementById(name_container_centro);
        if (!circle_centro_div_white) {
            throw new Error("Error in element id: " + name_container_centro);
        }
        const colorMap = {
            "A": "#66a3ff", // Azul A
            "D": "#ffa366", // Naranja claro D
            "L": "#a366ff", // Violeta claro L
            "O": "#E592FF"  // Rosado claro O
        };
        // Identificar qué nivel aplica
        var targetLevel = null;

        if(levels["A"]==texts.length){
            targetLevel = "A";
            circle_centro_div_white.style.backgroundColor=colorMap[targetLevel];
        }
         if(levels["D"]==texts.length){
            targetLevel = "D";
            circle_centro_div_white.style.backgroundColor=colorMap[targetLevel];
        }
         if(levels["L"]==texts.length){
            targetLevel = "L";
            circle_centro_div_white.style.backgroundColor=colorMap[targetLevel];
        }
        if(levels["O"]==texts.length){
            targetLevel = "O";
            circle_centro_div_white.style.backgroundColor=colorMap[targetLevel];
        }
        
    } catch (error) {
        console.error("Error in comprobar_color function:", error);
    }
};


window.circle_1 = function () {
    try {
        var name_container_padre = window.name_container_padre_1;
        var name_container_centro="centro_circle_1";
        var texts = window.texts_1;
        var numberOfCircles = window.numberOfCircles_1;
        var competencia_ok_for_user = window.competencia_ok_for_user_1;
        var ids_numbers = window.idnumbers;
        var ids_circles_cambio_color = [];
        var cabeceras = window.cabeceras_1[0]['cabecera'];
        var cabecera0 = 'cabecera_1';
        var count_cabecera = window.cabeceras_1.length;
        create_circle(ids_numbers, count_cabecera, name_container_principal_1, cabecera0, name_container_padre, name_container_centro, texts, numberOfCircles, competencia_ok_for_user, ids_circles_cambio_color, cabeceras);
    } catch (error) {
        console.error("Error en circle_1: ", error);
    }
};

window.circle_2 = function () {
    try {
        var name_container_padre = window.name_container_padre_2;
        var name_container_centro="centro_circle_2";
        var texts = window.texts_2;
        var numberOfCircles = window.numberOfCircles_2;
        var competencia_ok_for_user = window.competencia_ok_for_user_2;
        var ids_circles_cambio_color = [];
        var cabeceras = window.cabeceras_1[1]['cabecera'];
        var cabecera0 = 'cabecera_2';
        var count_cabecera = window.cabeceras_2.length;
        var ids_numbers = window.idnumbers;
        create_circle(ids_numbers, count_cabecera, name_container_principal_2, cabecera0, name_container_padre, name_container_centro, texts, numberOfCircles, competencia_ok_for_user, ids_circles_cambio_color, cabeceras);
    } catch (error) {
        console.error("Error en circle_2: ", error);
    }
};

window.circle_3 = function () {
    try {
        var name_container_padre = window.name_container_padre_3;
        var name_container_centro="centro_circle_3";
        var texts = window.texts_3;
        var numberOfCircles = window.numberOfCircles_3;
        var competencia_ok_for_user = window.competencia_ok_for_user_3;
        var ids_circles_cambio_color = [];
        var cabeceras = window.cabeceras_1[2]['cabecera'];
        var cabecera0 = 'cabecera_3';
        var count_cabecera = window.cabeceras_2.length;
        var ids_numbers = window.idnumbers;
        create_circle(ids_numbers, count_cabecera, name_container_principal_3, cabecera0, name_container_padre, name_container_centro, texts, numberOfCircles, competencia_ok_for_user, ids_circles_cambio_color, cabeceras);
    } catch (error) {
        console.error("Error en circle_3: ", error);
    }
};
window.circle_4 = function () {
    try {
        var name_container_padre = window.name_container_padre_4;
        var name_container_centro="centro_circle_4";
        var texts = window.texts_4;
        var numberOfCircles = window.numberOfCircles_4;
        var competencia_ok_for_user = window.competencia_ok_for_user_4;
        var ids_circles_cambio_color = [];
        var cabeceras = window.cabeceras_1[3]['cabecera'];
        var cabecera0 = 'cabecera_4';
        var count_cabecera = window.cabeceras_4.length;
        var ids_numbers = window.idnumbers;
        create_circle(ids_numbers, count_cabecera, name_container_principal_4, cabecera0, name_container_padre, name_container_centro, texts, numberOfCircles, competencia_ok_for_user, ids_circles_cambio_color, cabeceras);
    } catch (error) {
        console.error("Error en circle_4: ", error);
    }
};

window.circle_5 = function () {
    try {
        var name_container_padre = window.name_container_padre_5;
        var name_container_centro="centro_circle_5";
        var texts = window.texts_5;
        var numberOfCircles = window.numberOfCircles_5;
        var competencia_ok_for_user = window.competencia_ok_for_user_5;
        var ids_circles_cambio_color = [];
        var cabeceras = window.cabeceras_1[4]['cabecera'];
        var cabecera0 = 'cabecera_5';
        var count_cabecera = window.cabeceras_5.length;
        var ids_numbers = window.idnumbers;
        create_circle(ids_numbers, count_cabecera, name_container_principal_5, cabecera0, name_container_padre, name_container_centro, texts, numberOfCircles, competencia_ok_for_user, ids_circles_cambio_color, cabeceras);
    } catch (error) {
        console.error("Error en circle_5: ", error);
    }
};

window.circle_6 = function () {
    try {
        var name_container_padre = window.name_container_padre_6;
        var name_container_centro="centro_circle_6";
        var texts = window.texts_6;
        var numberOfCircles = window.numberOfCircles_6;
        var competencia_ok_for_user = window.competencia_ok_for_user_6;
        var ids_circles_cambio_color = [];
        var cabeceras = window.cabeceras_1[5]['cabecera'];
        var cabecera0 = 'cabecera_6';
        var count_cabecera = window.cabeceras_5.length;
        var ids_numbers = window.idnumbers;
        create_circle(ids_numbers, count_cabecera, name_container_principal_6, cabecera0, name_container_padre, name_container_centro, texts, numberOfCircles, competencia_ok_for_user, ids_circles_cambio_color, cabeceras);
    } catch (error) {
        console.error("Error en circle_6: ", error);
    }
};

window.circle_7 = function () {
    try {
        var name_container_padre = window.name_container_padre_7;
        var name_container_centro="centro_circle_7";
        var texts = window.texts_7;
        var numberOfCircles = window.numberOfCircles_7;
        var competencia_ok_for_user = window.competencia_ok_for_user_7;
        var ids_circles_cambio_color = [];
        var cabeceras = window.cabeceras_1[6]['cabecera'];
        var cabecera0 = 'cabecera_7';
        var count_cabecera = window.cabeceras_8.length;
        var ids_numbers = window.idnumbers;
        create_circle(ids_numbers, count_cabecera, name_container_principal_7, cabecera0, name_container_padre, name_container_centro, texts, numberOfCircles, competencia_ok_for_user, ids_circles_cambio_color, cabeceras);
    } catch (error) {
        console.error("Error en circle_7: ", error);
    }
};

window.circle_8 = function () {
    try {
        var name_container_padre = window.name_container_padre_8;
        var name_container_centro="centro_circle_8";
        var texts = window.texts_8;
        var numberOfCircles = window.numberOfCircles_8;
        var competencia_ok_for_user = window.competencia_ok_for_user_8;
        var ids_circles_cambio_color = [];
        var cabeceras = window.cabeceras_1[7]['cabecera'];
        var cabecera0 = 'cabecera_8';
        var count_cabecera = window.cabeceras_8.length;
        var ids_numbers = window.idnumbers;
        create_circle(ids_numbers, count_cabecera, name_container_principal_8, cabecera0, name_container_padre, name_container_centro, texts, numberOfCircles, competencia_ok_for_user, ids_circles_cambio_color, cabeceras);
    } catch (error) {
        console.error("Error en circle_8: ", error);
    }
};

// Función para crear la leyenda de las areas
window.create_legend = function() {
    var levels = window.legend;
    var container = document.getElementById('legend_container');

    const table = document.createElement("table");

    // Agrega el encabezado
    const headerRow = document.createElement("tr");
    table.appendChild(headerRow);
    // Agrega las filas de datos
    levels.slice(0, 3).forEach((level, index) => {
        const row = document.createElement("tr");
        row.className = "level-row";
        row.id = `level-${index}`;

        // Celda de color
        const colorCell = document.createElement("td");
        const colorBox = document.createElement("div");
        colorBox.style.width = "20px";
        colorBox.style.height = "20px";
        colorBox.style.backgroundColor = level.color;
        colorBox.style.border = "1px solid black";
        colorBox.style.borderRadius = "50%";
        colorCell.appendChild(colorBox);
        row.appendChild(colorCell);

        // Celda de nombre
        const nameCell = document.createElement("td");
        nameCell.textContent = level.name;
        row.appendChild(nameCell);
        // Añadir la fila a la tabla
        table.appendChild(row);
    });

    // Combina los últimos dos niveles
    if (levels.length > 3) {
        const combinedLevel = {
            color: levels[3].color,
            name: levels.slice(3).map(level => level.name).join(' & ')
        };

        const row = document.createElement("tr");
        row.className = "level-row";
        row.id = `level-3`;
        // Celda de color
        const colorCell = document.createElement("td");
        const colorBox = document.createElement("div");
        colorBox.style.width = "20px";
        colorBox.style.height = "20px";
        colorBox.style.backgroundColor = combinedLevel.color;
        colorBox.style.border = "1px solid black";
        colorBox.style.borderRadius = "50%";
        colorCell.appendChild(colorBox);
        row.appendChild(colorCell);

        // Celda de nombre
        const nameCell = document.createElement("td");
        nameCell.textContent = combinedLevel.name;
        row.appendChild(nameCell);
        // Añadir la fila a la tabla
        table.appendChild(row);
    }
    // Añade la tabla al contenedor
    container.appendChild(table);
};

create_legend();
