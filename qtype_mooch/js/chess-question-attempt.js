import { Chessground } from "./chessground/chessground.js";
import { clone, ensureProperty, createChessgroundShape, setTheme, isTrue } from "./util.js";
import { Fen } from "./Fen.js";

/**
 * @param config_default (object) Default configration object that is passed to the constructor of the chessboard.
 * @param theme (object) Information about which board and pieces to use
 */
export function init(config_default, theme) {
    ensureProperty(config_default, "drawable.pieces");
   
    let boards = document.querySelectorAll("[data-qtype-mooch-id-question-board]");
    for (let i = 0; i < boards.length; ++i) {
        let config = clone(config_default);
        let chesswidget = boards[i];
        let uid = chesswidget.getAttribute("data-qtype-mooch-id-question-board");
        let attempt = document.querySelector(`[data-qtype-mooch-id-attempt-field='${uid}']`);
        let fen = new Fen(chesswidget.getAttribute("data-qtype-mooch-question-fen"));
        let answer = chesswidget.getAttribute("data-qtype-mooch-question-answer");
        let correct = chesswidget.getAttribute("data-qtype-mooch-question-correct");
        let moveMarker = document.querySelector(`[data-qtype-mooch-id-move-marker='${uid}'`);
        moveMarker.classList.add(fen.color);
        moveMarker.innerHTML = M.str.qtype_mooch[fen.color == 'white' ? 'whitetomove' : 'blacktomove'];
        chesswidget.appendChild(moveMarker);

        let cgwrap = document.createElement("DIV");
        chesswidget.appendChild(cgwrap);
       
        ensureProperty(config, "movable");
        config.movable.color = fen.color;
        config.fen = fen.position;
        config.turnColor = fen.color;
        let chessboard = new Chessground(cgwrap, config);
       
        setTheme(chesswidget, theme);
       
        if (!config.viewOnly) {
            ensureProperty(config, "movable.events");
            config.movable.events.after = (orig, dest, metadata) => onMove(chessboard, config, chesswidget, attempt, orig, dest, metadata);
        }
       
        if (answer) {
            ensureProperty(config, "drawable");
            if (isTrue(correct)) {
                config.drawable.autoShapes = [
                    createChessgroundShape(attempt.value, fen.color, "green")
                ]
            } else {
                config.drawable.autoShapes = [
                    createChessgroundShape(answer, fen.color, "blue"),
                    createChessgroundShape(attempt.value, fen.color, "red"),
                ];
            }
        }
       
        chessboard.set(config);

        let reset = document.querySelector("[data-qtype-mooch-id-reset-button='" + uid + "']");
        if (reset) {
            reset.addEventListener("click", (event) => {
                event.preventDefault();
                onReset(chessboard, config, attempt, fen);
            });
        }
    }
}

/**
 * @param chessboard (Chessground) The chessboard on which the move ocurred
 * @param config (object) The configuration of the chessboard
 * @param input (HTMLElement) The element to which to write the move
 * @param orig (string) The square from which the moved piece came from
 * @param dest (string) The square to which  the piece moved
 * @param metadata Additional data for the move
 */
function onMove(chessboard, config, boardElement, input, orig, dest, _metadata) {
    if (isPromotion(chessboard, orig, dest)) {
        let color = chessboard.state.pieces.get(dest).color;
        choosePromotionPiece(boardElement, color, config)
            .then(promotedPiece => {
                const letters = { pawn: 'p', rook: 'r', knight: 'n', bishop: 'b', queen: 'q', king: 'k' };
                let pieces = new Map(chessboard.state.pieces);
                pieces.set(dest, promotedPiece);
                chessboard.setPieces(pieces);
                let letter = letters[promotedPiece.role].toUpperCase();
                input.value = `${orig}-${dest}=${letter}`;
            })
            .catch(() => {
                chessboard.set(config);
            })
    } else if (isCastle(chessboard, orig, dest)) {
        input.value = (dest.charAt(0) == 'g' ? '0-0' : '0-0-0');
    } else {
        input.value = `${orig}-${dest}`;
    }
    return true;
}

function onReset(chessboard, config, input, fen) {
    config.turnColor = fen.color;
    chessboard.set(config);
    input.value = "";
}

/**
 * Show a modal Dialog that prompst for choosing a piece to promote a pawn to.
 *
 * @param board HTMLElement  The parent element of the dialog
 * @param color string The color that moved, either 'w' for White or 'b' for black
 * @return Promise
 */
function choosePromotionPiece(board, color, config) {
    return new Promise((onfulfilled, onrejected) => {
        let c = color.charAt(0);
       
        let btClose = document.createElement('BUTTON');
        btClose.innerHTML = "🗙";
       
        let title = document.createElement('DIV');
        title.style['text-align'] = 'right';
        title.appendChild(btClose);

        let btQueen = document.createElement('BUTTON');
        btQueen.innerHTML = (c == 'b') ? '♛':'♕';
        btQueen.classList.add(color, 'queen', 'piece');
        let btRook = document.createElement('BUTTON');
        btRook.innerHTML = (c == 'b') ? '♜':'♖';
        btRook.classList.add(color, 'rook', 'piece');
        let btBishop = document.createElement('BUTTON');
        btBishop.innerHTML = (c == 'b') ? '♝':'♗';
        btBishop.classList.add(color, 'bishop', 'piece');
        let btKnight = document.createElement('BUTTON');
        btKnight.innerHTML = (c == 'b') ? '♞':'♘';
        btKnight.classList.add(color, 'knight', 'piece');
        let content = document.createElement('DIV');
        content.appendChild(btQueen);
        content.appendChild(btRook);
        content.appendChild(btBishop);
        content.appendChild(btKnight);

        let dialog = document.createElement('DIV');
        dialog.classList.add('promotionbox');
        dialog.appendChild(title);
        dialog.appendChild(content);
        dialog.style['position'] = 'absolute';
        dialog.style['background-color'] = 'white';
        dialog.style['visibility'] = 'hidden';
        dialog.style['z-index'] = '100';
        board.appendChild(dialog);

        let board_width = board.firstElementChild.clientWidth;
        let board_height = board.firstElementChild.clientHeight;
        let dlg_width = 3 * btQueen.offsetWidth;
        let top_offset = 0;
        let left_offset = 0;

        top_offset = board_height / 2;
        left_offset = (board_width - dlg_width) / 2;

        dialog.style['top'] = `${top_offset}px`;
        dialog.style['left'] = `${left_offset}px`;
        dialog.style['visibility'] = 'visible';

        btClose.addEventListener('click', () => {
            dialog.remove();
            onrejected();
        });
        btQueen.addEventListener('click', () => {
            dialog.remove();
            onfulfilled({color: color, role: "queen"});
        })
        btRook.addEventListener('click', () => {
            dialog.remove();
            onfulfilled({color: color, role: "rook"});
        })
        btBishop.addEventListener('click', () => {
            dialog.remove();
            onfulfilled({color: color, role: "bishop"});
        })
        btKnight.addEventListener('click', () => {
            dialog.remove();
            onfulfilled({color: color, role: "knight"});
        })
    });
}

/**
 * Test whether the move is a pawn promotion.
 *
 * @param chessboard The chessboard
 * @param _from The original square of the piece
 * @param to The destination square of the piece
 * @return boolean
 */
function isPromotion(chessboard, _from, to) {
    let piece = chessboard.state.pieces.get(to);
    let rank = to.charAt(1);
    let promotion_rank = (piece.color == 'white' ? 8 : 1);
    return (rank == promotion_rank && piece.role == 'pawn');
}

/**
 * Test whether the move is considered a castling move.
 *
 * A move is considered a castling move if a king moves from its starting square
 * two squares to the side, a rook in that direction is on its starting square
 * and there are no pieces between the king and the rook. 
 *
 * @param chessboard The chessboard
 * @param from The original square of the piece
 * @param to The destination square of the piece
 * @return boolean
 */
function isCastle(chessboard, from, to) {
    let piece = chessboard.state.pieces.get(to);
    if (piece.role !== 'king') {
        return false;
    }

    let rank = (piece.color === 'white' ? '1' : '8');
    if (from === `e${rank}`) {
        if (to === `g${rank}`) {
            let rook = chessboard.state.pieces.get(`f${rank}`);
            return typeof rook !== "undefined" && rook.color === piece.color && rook.role === "rook" &&
                typeof chessboard.state.pieces.get(`h${rank}`) === "undefined";
        } else if (to == `c${rank}`) {
            let rook = chessboard.state.pieces.get(`d${rank}`);
            return typeof rook !== "undefined" && rook.color === piece.color && rook.role === "rook" &&
                typeof chessboard.state.pieces.get(`b${rank}`) === "undefined" &&
                typeof chessboard.state.pieces.get(`a${rank}`) === "undefined";
        }
    }

    return false;
}
