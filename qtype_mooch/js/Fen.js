/**
 * A class to handle Forsyth-Edwards notation.
 *
 * @package    qtype
 * @subpackage mooch
 * @copyright  2021 Oswald Jaskolla
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export class Fen {
    /**
     * @param fen (string) A chess position in Forsyth-Edwards notation
     */
    constructor(fen) {
        this.fen = fen;
    }

    /**
     * @param fen (string) A chess position in Forsyth-Edwards notation
     */
    set fen(fen) {
        let parts = fen.split(/\s+/);
        switch (parts.length) {
          case 6:
            this.fullmovenumber = parts[5];
          case 5:
            this.halfmoveclock = parts[4]
          case 4:
            this.enpassant = parts[3];
          case 3:
            this.castles = parts[2];
          case 2:
            this.color = parts[1] == "w" ? "white" : "black";
          case 1:
            this.position = parts[0];
            break;
          default:
            throw fen;
        }       
    }

    /**
     * @return string The chess position in Forsyth-Edwards notation
     */
    get fen() {
        return this.toString();
    }

    toString() {
        let c = (value, alternative) => {
            return (typeof value !== 'undefined' && value) ? value : alternative;
        }
        
        let fen = this.position;
        fen += " " + c(this.color, "-").charAt(0);
        fen += " " + c(this.castles, "-");
        fen += " " + c(this.enpassant, "-");
        fen += " " + c(this.halfmoveclock, "0");
        fen += " " + c(this.fullmovenumber, "1");
        return fen;
    }
}

/**
 * @param fen (string) A chess position in Forsyth-Edwards notation
 * @return string The position of the pieces in Forsyth-Edwards notation, empty
 *  string if the position is invalid in the sense that it cannot be determined
 *  which piece is on which square.
 */
Fen.validatePosition = (fen) => {
    let valid = true;
    let ranks = 0;
    fen.trim().split("/").forEach((element, _index) => {
        ranks += 1;
        let files = 0;
        for (const c of element) {
            if ("012345678".search(c) >= 0) {
                files += parseInt(c);
            } else if ("KQRBNPkqrbnp".search(c) >= 0) {
                files += 1;
            } else if (c.trim() != c) {
                break;
            } else {
                valid = false;
                return;
            }
        }
        if (files != 8) {
            valid = false;
            return;
        }
    });

    if (ranks == 8 && valid) {
        return fen.trim();
    } else {
        return "";
    }   
}