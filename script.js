/* ============================================
   METAL LYRICS GENERATOR - JAVASCRIPT
   MIT VOLLSTÄNDIGER PREMIUM-IMPLEMENTIERUNG
   + GENRE PERSPECTIVES
   ============================================ */

// ===== KONFIGURATION =====
const CONFIG = {
    DEMO_MODE: false, // Auf FALSE für echte API-Calls!
    API_BASE: 'api/', // Basis-URL für API-Calls
};

// ===== DOM-ELEMENTE =====
const form = document.getElementById('lyric-form');
const generateBtn = document.getElementById('generate-btn');
const btnText = generateBtn.querySelector('.btn-text');
const btnLoader = generateBtn.querySelector('.btn-loader');
const resultsSection = document.getElementById('results-section');
const lyricsTitle = document.getElementById('lyrics-title');
const lyricsContent = document.getElementById('lyrics-content');
const resultMythology = document.getElementById('result-mythology');
const resultGenre = document.getElementById('result-genre');
const copyBtn = document.getElementById('copy-btn');
const regenerateBtn = document.getElementById('regenerate-btn');
const newThemeBtn = document.getElementById('new-theme-btn');
const usageCount = document.getElementById('usage-count');
const premiumCodeInput = document.getElementById('premium-code-input');
const activatePremiumBtn = document.getElementById('activate-premium-btn');
const premiumStatus = document.getElementById('premium-status');

// ===== PREMIUM STATE =====
let isPremiumUser = false;
let remainingGenerations = 5;

// ===== PREMIUM STATUS LADEN =====
async function loadPremiumStatus() {
    if (CONFIG.DEMO_MODE) {
        isPremiumUser = false;
        updatePremiumUI();
        return;
    }

    try {
        const response = await fetch(CONFIG.API_BASE + 'check-premium.php');
        const data = await response.json();

        isPremiumUser = data.isPremium;
        window.premiumExpiresAt = data.expiresAt;
        updatePremiumUI();

    } catch (error) {
        console.error('Error loading premium status:', error);
    }
}

// ===== ACTIVATE PREMIUM CODE =====
async function activatePremiumCode() {
    const code = premiumCodeInput.value.trim();

    if (!code) {
        showPremiumMessage('Please enter a code', 'error');
        return;
    }

    if (CONFIG.DEMO_MODE) {
        showPremiumMessage('Demo mode: Please activate the API (DEMO_MODE: false)', 'error');
        return;
    }

    try {
        activatePremiumBtn.disabled = true;
        activatePremiumBtn.textContent = '⏳ Checking...';
        
        const response = await fetch(CONFIG.API_BASE + 'check-premium.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'activate',
                code: code
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            isPremiumUser = true;
            window.premiumExpiresAt = data.expiresAt;
            premiumCodeInput.value = '';
            showPremiumMessage(data.message, 'success');
            updatePremiumUI();
        } else {
            showPremiumMessage(data.message, 'error');
        }
        
    } catch (error) {
        console.error('Error activating:', error);
        showPremiumMessage('Connection error. Please try again.', 'error');
    } finally {
        activatePremiumBtn.disabled = false;
        activatePremiumBtn.textContent = '🔓 Activate';
    }
}

// ===== UPDATE PREMIUM UI =====
function updatePremiumUI() {
    if (isPremiumUser) {
        let statusHTML = `
            <div class="premium-active">
                ✅ <strong>Premium Active</strong> - Unlimited Generations!
            </div>
        `;

        // Show expiration info for time-based codes
        if (window.premiumExpiresAt) {
            const expiresAt = new Date(window.premiumExpiresAt);
            const now = new Date();
            const remainingHours = Math.max(0, (expiresAt - now) / (1000 * 60 * 60));

            if (remainingHours > 0) {
                statusHTML += `
                    <div class="premium-info">
                        ⏱️ Valid for ${remainingHours.toFixed(1)} more hours
                    </div>
                `;
            }
        }

        premiumStatus.innerHTML = statusHTML;
        document.querySelector('.premium-activation').style.display = 'none';
        usageCount.parentElement.innerHTML = '<p><strong>Premium:</strong> ∞ Unlimited</p>';
    } else {
        premiumStatus.innerHTML = '';
        document.querySelector('.premium-activation').style.display = 'block';
        updateUsageDisplay();
    }
}

// ===== SHOW PREMIUM MESSAGE =====
function showPremiumMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `premium-message premium-message-${type}`;
    messageDiv.textContent = message;

    premiumStatus.appendChild(messageDiv);

    setTimeout(() => {
        messageDiv.remove();
    }, 5000);
}

// ===== USAGE TRACKING (only for Free Users) =====
function updateUsageDisplay() {
    if (isPremiumUser) {
        usageCount.textContent = '∞';
        return;
    }

    usageCount.textContent = Math.max(0, 5 - remainingGenerations);

    if (remainingGenerations <= 0) {
        generateBtn.disabled = true;
        generateBtn.innerHTML = '⚠️ Daily limit reached - Upgrade to Premium';
    }
}

// ===== PREMIUM MYTHOLOGY LIST =====
// All non-European mythologies require premium access
const PREMIUM_MYTHOLOGIES = [
    'japanese', 'chinese', 'hindu',           // Asian
    'aztec', 'mayan', 'african',              // Americas & Africa
    'egyptian', 'mesopotamian',                // Ancient Cultures
    'occult', 'lovecraft', 'gothic'           // Occult & Horror
];

// ===== PREMIUM STRUCTURE LIST =====
const PREMIUM_STRUCTURES = ['long', 'epic', 'progressive', 'concept'];

// ===== MYTHOLOGY DATA (erweitert) =====
const MYTHOLOGY_DATA = {
    norse: {
        name: "Norse",
        keywords: ["Odin", "Thor", "Ragnarök", "Valhalla", "Yggdrasil", "Fenrir", "Valkyries", "Asgard", "Frost Giants", "Runes"],
        tone: "epic, fatalistic, warlike"
    },
    celtic: {
        name: "Celtic",
        keywords: ["Druids", "Morrigan", "Tuatha Dé Danann", "Samhain", "Otherworld", "Sacred Groves", "Ravens", "Mist", "Ancient Stones"],
        tone: "mystical, nature-connected, dark"
    },
    greek: {
        name: "Greek",
        keywords: ["Zeus", "Hades", "Olympus", "Titans", "Underworld", "Lightning", "Fates", "Heroes", "Hydra", "Medusa"],
        tone: "heroic, dramatic, divine"
    },
    occult: {
        name: "Occult",
        keywords: ["Demons", "Rituals", "Pentagrams", "Blood Moon", "Dark Arts", "Summoning", "Unholy", "Cursed", "Shadows", "Necromancy"],
        tone: "dark, forbidden, threatening"
    },
    egyptian: {
        name: "Egyptian",
        keywords: ["Ra", "Anubis", "Osiris", "Pyramids", "Scarabs", "Pharaohs", "Book of the Dead", "Desert", "Sands of Time", "Ammit"],
        tone: "majestic, timeless, mysterious"
    },
    lovecraft: {
        name: "Lovecraft",
        keywords: ["Cthulhu", "Elder Gods", "Madness", "Eldritch", "R'lyeh", "Tentacles", "Cosmic Horror", "Ancient Ones", "Insanity"],
        tone: "insane, cosmic, ancient horror"
    }
};

// ===== GENRE DATA MIT PERSPECTIVES =====
const GENRE_DATA = {
    thrash: {
        name: "Thrash Metal",
        style: "fast, aggressive, precise",
        structure: "short, punchy lines with power",
        perspective: "Anger and accusation. The theme is portrayed as a battle - against enemies, oppression or fate. Direct, confrontational tone. No metaphor detours, just in-your-face aggression.",
        thematic_lens: "How would an angry warrior see this theme? What makes them furious?"
    },
    death: {
        name: "Death Metal",
        style: "brutal, technical, dark",
        structure: "complex imagery, dark metaphors",
        perspective: "Brutality and finality. The theme is shown in its darkest, unvarnished form. Decay, damnation, consequences. No hope, no redemption, only the harsh reality.",
        thematic_lens: "What is the most cruel, most final version of this theme? Show the consequences without filter."
    },
    black: {
        name: "Black Metal",
        style: "atmospheric, cold, raw",
        structure: "poetic-dark, nature-connected",
        perspective: "Coldness and transcendence. The theme is filtered through forces of nature and cosmic loneliness. Fog, frost, emptiness, forests. Sublime but bleak, misanthropic.",
        thematic_lens: "How does this theme feel in a cold, godless wilderness? What loneliness does it bring?"
    },
    power: {
        name: "Power Metal",
        style: "epic, melodic, heroic",
        structure: "large choruses, heroic narratives",
        perspective: "Triumph and honor. The theme is portrayed as a heroic quest. Even tragedies become glorious sacrifice. Hope and victory are always possible, heroes stand together.",
        thematic_lens: "How would a hero experience this theme as their greatest moment? Where lies the triumph?"
    },
    doom: {
        name: "Doom Metal",
        style: "slow, heavy, melancholic",
        structure: "long, heavy lines, dark",
        perspective: "Grief and acceptance. The theme is viewed as inevitable fate. Slow sinking, heavy burden, resigned beauty. Time stretches, everything weighs heavy.",
        thematic_lens: "How does the slow, unstoppable weight of this theme feel? What grief does it carry?"
    },
    folk: {
        name: "Folk Metal",
        style: "nature-connected, traditional",
        structure: "stories, nature metaphors",
        perspective: "Tradition and cycle. The theme is seen as part of a larger cycle. Ancestors, seasons, rituals, community. Melancholic but connected to roots and heritage.",
        thematic_lens: "How would the ancients have sung about this theme and passed it to their children? What story does it tell?"
    }

     heavy: {
        name: "Heavy Metal",
        style: "classic, melodic, driving riffs",
        structure: "anthem-like verses, strong choruses",
        perspective: "Defiance and resolve. The theme becomes a personal stand against adversity, powered by determination and iron will. Bold statements, confident tone.",
        thematic_lens: "How would a steadfast warrior or rebel declare this theme as their personal creed?"
    },

    metalcore: {
        name: "Metalcore",
        style: "modern, emotional, breakdown-heavy",
        structure: "dynamic contrast between soft and heavy, sharp rhythmic hits",
        perspective: "Inner conflict and catharsis. The theme is framed as a struggle within: pain, doubt, resilience, emotional release. Raw, honest, modern.",
        thematic_lens: "What internal battle does this theme create? What pain gets released when everything breaks open?"
    },

    gothic: {
        name: "Gothic Metal",
        style: "melancholic, dark, atmospheric",
        structure: "romantic-dark verses, flowing imagery",
        perspective: "Sorrow and longing. The theme is filtered through melancholic beauty, shadows, candlelight, loss, and emotional depth. Elegance mixed with tragedy.",
        thematic_lens: "What hidden sadness or forbidden desire lies within this theme? What beauty exists within the darkness?"
    }
};

// ===== DEMO LYRICS MIT GENRE PERSPECTIVES =====
const DEMO_LYRICS_TEMPLATES = {
    norse_thrash: {
        title: "Blades of the North",
        content: `[Verse 1]
Through blizzards of steel and blood-red snow
The Allfather's ravens scream below
Warriors clash with hammer and axe
No mercy shown, no turning back

[Chorus]
We ride to Valhalla's burning gate!
With thunder and fury we seal our fate!
The Valkyries call through the crimson sky
Tonight we fight, tonight we die!`
    },
    occult_black: {
        title: "Rituals of the Void",
        content: `[Verse 1]
In moonless night the circle forms
Ancient words through darkness swarms
Candles flicker, shadows dance
The veil grows thin, we fall in trance

[Chorus]
From the depths we call your name
Elder darkness, endless flame
Through the void, through blackest night
We embrace the absence of light`
    },
    greek_power: {
        title: "Heroes of Olympus",
        content: `[Verse 1]
On mountain peaks where gods reside
With lightning strike and burning pride
We stand united, shields ablaze
To fight until the end of days

[Chorus]
We are the heroes of Olympus!
Thunder and glory unite us!
With fire in our hearts we stand
Defenders of this sacred land!`
    },
    egyptian_folk: {
        title: "Journey to the Western Shores",
        content: `[Verse 1]
Across the Nile where shadows fade
The jackal guides what time has made
In linen wrapped, the sacred sleep
Where Osiris waits, his promise to keep

[Chorus]
Sail on, sail on to the western shores
Where the sun descends and the spirit soars
Our ancestors call from the field of reeds
In death we bloom like lotus seeds`
    },
    egyptian_death: {
        title: "Devoured by Ammit",
        content: `[Verse 1]
Your heart upon the scales of Ma'at
Heavy with the sins you've wrought
Anubis grins with jackal teeth
No afterlife, no relief

[Chorus]
Ammit waits with hungry jaws
Crocodile mouth and lion claws
Your soul erased, devoured whole
Eternal nothing claims your soul`
    }
};

// ===== DEMO-MODUS: Simulierte Lyrics (mit Perspektiven) =====
function generateDemoLyrics(mythology, genre, theme) {
    return new Promise((resolve) => {
        setTimeout(() => {
            const templateKey = `${mythology}_${genre}`;
            const template = DEMO_LYRICS_TEMPLATES[templateKey];
            
            if (template) {
                resolve({
                    title: template.title,
                    content: template.content
                });
            } else {
                const mythData = MYTHOLOGY_DATA[mythology];
                const genreData = GENRE_DATA[genre];
                
                resolve({
                    title: `${theme} of ${mythData.name}`,
                    content: `[DEMO MODE - Verse 1]
${mythData.keywords[0]} rises from the depths
${theme} echoes through the night
With ${mythData.keywords[1]} by our side
We embrace the ${genreData.style} fight

[DEMO MODE - Chorus]
${theme.toUpperCase()}! ${theme.toUpperCase()}!
The ${mythData.name} powers call!
In ${genreData.name} style we stand tall!

[Genre Perspective: ${genreData.name}]
${genreData.perspective}

[Note: DEMO mode active
Set DEMO_MODE: false for real AI lyrics
with full genre differentiation!]`
                });
            }
        }, 2000);
    });
}

// ===== PROMPT GENERATOR MIT GENRE PERSPECTIVES =====
function createPrompt(mythology, genre, theme, structure, mythData, genreData) {
    return `You are an expert Metal lyricist. Create authentic ${genreData.name} lyrics based on ${mythData.name} mythology.

USER'S THEME: "${theme}"

CRITICAL - GENRE PERSPECTIVE:
${genreData.perspective}

When writing about "${theme}", ask yourself: ${genreData.thematic_lens}

MYTHOLOGY CONTEXT:
- Culture: ${mythData.name}
- Key elements to use: ${mythData.keywords.slice(0, 6).join(', ')}
- Cultural tone: ${mythData.tone}

GENRE REQUIREMENTS:
- Musical style: ${genreData.style}
- Lyrical structure: ${genreData.structure}
- Apply the genre perspective to transform "${theme}" accordingly

STRUCTURE:
${structure === 'short' ? '1 verse + 1 chorus' : structure === 'medium' ? '2 verses + chorus' : '3 verses + chorus + bridge'}

FORMAT:
- Start with "Title: [Your Title]" on the first line
- Use [Verse 1], [Chorus], etc. as section markers
- English language
- Sound authentic, not generic

IMPORTANT: The same theme "${theme}" should feel COMPLETELY DIFFERENT in ${genreData.name} than in other genres. Make sure your lyrics clearly reflect the ${genreData.name} perspective described above.

Write the song now:`;
}

// ===== MAIN FUNCTION: GENERATE LYRICS =====
async function generateLyrics(mythology, genre, theme, structure) {
    if (CONFIG.DEMO_MODE) {
        return generateDemoLyrics(mythology, genre, theme);
    }

    const mythData = MYTHOLOGY_DATA[mythology];
    const genreData = GENRE_DATA[genre];

    const prompt = createPrompt(mythology, genre, theme, structure, mythData, genreData);

    const response = await fetch(CONFIG.API_BASE + 'generate-lyrics.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ prompt, mythology, genre, theme })
    });

    if (!response.ok) {
        const errorData = await response.json().catch(() => ({}));

        if (response.status === 429) {
            throw new Error('LIMIT_REACHED');
        }

        throw new Error(errorData.message || 'API Error');
    }
    
    const data = await response.json();

    // Update remaining from backend
    if (data.metadata && data.metadata.remaining_free !== undefined) {
        remainingGenerations = data.metadata.remaining_free;
    }

    return {
        title: data.title || 'Untitled',
        content: data.lyrics
    };
}

// ===== FORM SUBMIT =====
form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const mythology = document.getElementById('mythology').value;
    const genre = document.getElementById('genre').value;
    const theme = document.getElementById('theme').value;
    const structure = document.getElementById('structure').value;

    if (!mythology || !genre || !theme) {
        alert('Please fill in all fields!');
        return;
    }

    // Check if mythology requires premium access
    if (PREMIUM_MYTHOLOGIES.includes(mythology) && !isPremiumUser) {
        alert('🔒 Premium Required!\n\nThis mythology is only available for Premium users.\n\nEuropean mythologies (Norse, Celtic, Greek, Slavic) are free for everyone!\n\nEnter a Premium code below or contact us to get access to all mythologies.');
        document.getElementById('premium-code-input').focus();
        return;
    }

    // Check if structure requires premium access
    if (PREMIUM_STRUCTURES.includes(structure) && !isPremiumUser) {
        alert('🔒 Premium Required!\n\nThis song structure is only available for Premium users.\n\nFree structures: Short, Medium\nPremium structures: Long, Epic, Progressive, Concept\n\nEnter a Premium code below or contact us to upgrade!');
        document.getElementById('premium-code-input').focus();
        return;
    }

    setLoadingState(true);

    try {
        const lyrics = await generateLyrics(mythology, genre, theme, structure);
        displayLyrics(lyrics, mythology, genre);
        updateUsageDisplay();
        resultsSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    } catch (error) {
        console.error('Error:', error);

        if (error.message === 'LIMIT_REACHED') {
            alert('You have reached your daily limit! 🔒\n\nUpgrade to Premium for unlimited generations.\n\nEnter a Premium code or contact us for a code.');
            document.getElementById('premium-code-input').focus();
        } else {
            alert('Error: ' + error.message);
        }
    } finally {
        setLoadingState(false);
    }
});

// ===== HELPER FUNCTIONS =====
function displayLyrics(lyrics, mythology, genre) {
    lyricsTitle.textContent = lyrics.title;
    lyricsContent.textContent = lyrics.content;
    resultMythology.textContent = MYTHOLOGY_DATA[mythology].name;
    resultGenre.textContent = GENRE_DATA[genre].name;
    resultsSection.style.display = 'block';
}

function setLoadingState(isLoading) {
    generateBtn.disabled = isLoading;
    btnText.style.display = isLoading ? 'none' : 'inline';
    btnLoader.style.display = isLoading ? 'inline' : 'none';
}

// ===== COPY BUTTON =====
copyBtn.addEventListener('click', () => {
    const text = `${lyricsTitle.textContent}\n\n${lyricsContent.textContent}`;
    navigator.clipboard.writeText(text).then(() => {
        const original = copyBtn.textContent;
        copyBtn.textContent = '✅ Copied!';
        copyBtn.style.backgroundColor = '#2ecc71';
        setTimeout(() => {
            copyBtn.textContent = original;
            copyBtn.style.backgroundColor = '';
        }, 2000);
    });
});

// ===== REGENERATE & NEW THEME =====
regenerateBtn.addEventListener('click', () => form.dispatchEvent(new Event('submit')));
newThemeBtn.addEventListener('click', () => {
    resultsSection.style.display = 'none';
    document.getElementById('theme').focus();
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

// ===== PREMIUM CODE BUTTON =====
activatePremiumBtn.addEventListener('click', activatePremiumCode);
premiumCodeInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        activatePremiumCode();
    }
});
// ===== EXPORT ALS TXT =====
document.getElementById('export-txt-btn').addEventListener('click', () => {
    const title = lyricsTitle.textContent || 'metal-lyrics';
    const text = `${title}\n\n${lyricsContent.textContent}`;

    const blob = new Blob([text], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);

    const link = document.createElement('a');
    link.href = url;
    link.download = title.replace(/[^a-z0-9]/gi, '_').toLowerCase() + ".txt";
    link.click();

    URL.revokeObjectURL(url);
});

// ===== INIT =====
loadPremiumStatus();

console.log(
    CONFIG.DEMO_MODE
        ? '%c🎸 DEMO MODE - Genre Perspectives aktiv'
        : '%c🎸 LIVE MODE - Premium System + Genre Perspectives aktiv',
    'color: #c41e3a; font-size: 20px; font-weight: bold;'
);
