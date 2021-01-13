import { Chessground } from "./chessground/chessground.js";
import { Fen } from "./Fen.js"
import { clone, ensureProperty, setTheme } from "./util.js";

/**
 * @param config_default (object) Default configration object that is passed to the constructor of the chessboard.
 * @param theme (object) Information about which board and pieces to use 
 */
export function init(config_default, theme) {
    let fenfields = document.querySelectorAll("[data-qtype-mooch-id-fen-field]");

    for (let i = 0; i < fenfields.length; ++i) {
        let config = clone(config_default);
        let fenfield = fenfields[i];
        let fenReset = new Fen(fenfield.value);
        let fenCurrent = new Fen(fenfield.value); 
        let uid = fenfield.getAttribute("data-qtype-mooch-id-fen-field");
        let chesswidget = document.querySelector(`[data-qtype-mooch-id-preview-board='${uid}']`);
        let dragboxElement = document.querySelector(`[data-qtype-mooch-id-dragbox='${uid}']`);
        let turnElement = document.querySelector(`[data-qtype-mooch-id-turn-picker='${uid}']`);

        setTheme(chesswidget, theme);

        if (chesswidget) {
            let chessboardElement = document.createElement("DIV");
            chesswidget.appendChild(chessboardElement);

            let radioWhite = turnElement.querySelector("[value='w']");
            let radioBlack = turnElement.querySelector("[value='b']"); 
            (fenCurrent.color == "white" ? radioWhite : radioBlack).checked = true;
            let updateFenfield = (color) => {
                fenCurrent.color = color;
                fenfield.value = fenCurrent.toString();
            };
            radioWhite.addEventListener('click', () => updateFenfield("white"));
            radioBlack.addEventListener('click', () => updateFenfield("black"));

            let chessboard = new Chessground(chessboardElement, config);
            ensureProperty(config, ["events"]);
            config.events.change = () => {
                fenCurrent.position = chessboard.getFen();
                fenfield.value = fenCurrent.toString();
            };
            config.fen = fenCurrent.position;
            chessboard.set(config);

            fenfield.addEventListener("input", function() {
                let fen = Fen.validatePosition(fenfield.value);
                if (fen.length > 0) {
                    fenCurrent.fen = fenfield.value;
                    config.fen = fenCurrent.position;
                    chessboard.set(config);
                    (fenCurrent.color == "white" ? radioWhite : radioBlack).checked = true;
                }
            });

            chesswidget.querySelectorAll("[data-chessground-piece-color]").forEach((element, _index) => {
                let piece = {
                    color: element.getAttribute("data-chessground-piece-color"),
                    role: element.getAttribute("data-chessground-piece-role")
                };
                element.addEventListener("mousedown", (event) => {
                    event.preventDefault();
                    chessboard.dragNewPiece(piece, event, true);
                });
                element.addEventListener("click", (event) => {
                    event.preventDefault();
                });
            });
            
            chesswidget.querySelectorAll("[data-qtype-mooch-control]").forEach((element, _index) => {
                switch (element.getAttribute("data-qtype-mooch-control")) {
                  case "clear":
                    element.addEventListener("click", (event) => {
                        event.preventDefault();
                        fenCurrent.position = "8/8/8/8/8/8/8";
                        fenCurrent.color = "white";
                        fenfield.value = fenCurrent.toString(); 
                        config.fen = fenCurrent.position;
                        chessboard.set(config);
                        (fenCurrent.color == "white" ? radioWhite : radioBlack).checked = true;
                    });
                    break;
                  case "reset":
                    element.addEventListener("click", (event) => {
                        event.preventDefault();
                        fenCurrent.fen = fenReset.fen;
                        fenfield.value = fenCurrent.fen;
                        config.fen = fenCurrent.position;
                        chessboard.set(config);
                        (fenCurrent.color == "white" ? radioWhite : radioBlack).checked = true;
                    });
                    break;
                }
            });
        }
    }
}
