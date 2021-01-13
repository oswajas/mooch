/**
 * Clone an object.
 *
 * Anything that cannot be expressed in JSON wil not be part of the clone.
 *
 * @param object The object to clone
 * @return The clone  
 */
export function clone(object) {
    return JSON.parse(JSON.stringify(object));
}

/**
 * Ensures that an object has a nested property.
 *
 * @param obj The object
 * @param path String or array of strings. If an array is passed, each entry
 *  is treated as property of the previous entry. If a string is passed, it is
 *  split at "." into an array of strings and the function behaves as if that
 *  array was passed to the function.   
 */
export function ensureProperty(obj, path) {
    if (typeof path == "string") {
        path = path.split(".");
    }
    if (path.length > 0) {
        obj[path[0]] = obj[path[0]] || {};
        let child = obj[path[0]];
        path.shift();
        ensureProperty(child, path);
    }
}

/**
 * Sets a theme for the chessboard. 
 *
 * @param element (HTMLElement) The element on which to set the theme
 * @param theme (object) THeme information
 */
export function setTheme(element, theme) {
    if (theme.type == 'builtin') {
        element.classList.add(`pieces_${theme.builtin_pieces}`);
        element.classList.add(`board_${theme.builtin_board}`);
    }
}

/**
 * Normalize a move.
 *
 * Output format is
 *
 *   - Standard moves    [square]-[square]
 *   - Pawn promotion    [square]-[square]=[piececode]
 *   - King side castle  0-0-0
 *   - Queen side castle 0-0
 *
 * in lower case.
 *
 * @param string move The move
 * @return string A standard representation of the move
 */
function normalizeMove(move) {
    let parts = move.split("|");
    let matches = parts[0].match(/([abcdefgh][12345678]).*([abcdefgh][12345678])([^qrbn]*([qrbn]))?/i);
    if (matches) {
        move = `${matches[1]}-${matches[2]}`;
        if (matches.length >= 4) {
            move += `=${matches[4]}`;
        }
        return move.toLowerCase();
    } else if (parts[0].match(/[0Oo]\s*-\s*[0Oo]\s*-\s*[0oO]/)) {
        return "0-0-0";
    } else if (parts[0].match(/[0Oo]\s*-\s*[0oO]/)) {
        return "0-0";
    } else {
        return "";
    }
}

/**
 * Create a Shape object for a move to pass to Chessground.
 * 
 * @param move (string) The move
 * @param color (string) The side that moved, either 'white' or 'black'
 * @return Shape that can be passed to chessground. 
 */
export function createChessgroundShape(move, color, brush) {
    move = normalizeMove(move);

    let shape = {};

    if (move.startsWith("0-0-0")) {
        let rank = color == "white" ? 1 : 8;
        shape.orig = `e${rank}`;
        shape.dest = `c${rank}`;
    } else if (move.startsWith("0-0")) {
        let rank = color == "white" ? 1 : 8;
        shape.orig = `e${rank}`;
        shape.dest = `g${rank}`;
    } else if (move.length > 0) {
        let parts = move.split(/-|=/);
        shape.orig = parts[0];
        shape.dest = parts[1];
    }

    if (shape.dest) {
        shape.mouseSq = shape.dest;
        shape.brush = brush;
        shape.snapToValidMove = false;
        shape.pos = [1, 1];
    }

    return shape;
}

/**
 * A more linient way to teset for truth
 *
 * @param value The value to test for truth.
 * @return boolean Whether the value is considered true.
 */ 
export function isTrue(value) {
    if (!value) {
        return false;
    }

    if (typeof value === 'string') {
        value = value.trim();
        if (value.match(/^false$/i)) {
            return false;
        }

        if (value.match(/^off$/i)) {
            return false;
        }

        if (value.match(/^no$/i)) {
            return false;
        }
        
        if (value.match(/^[-+]*0*$/)) {
            return false;
        }
    }
    
    return true;
}