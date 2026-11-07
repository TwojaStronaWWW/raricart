#!/usr/bin/env node
/**
 * Cryptographic Seed String & Structural Entropy Generator (Anshu Chimala Technique 1)
 * Supports both random 48-byte entropy streams (`node generate-seed.js [count]`)
 * and deterministic named brand seeds (`node generate-seed.js --seed="Brand:Industry" [--json]`).
 * Forces the LLM out of next-token statistical mediocrity ("AI slop") BEFORE reading CSS.
 */
import crypto from 'node:crypto';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);

export const SPATIAL_ARCHETYPES = [
  'Asymmetric Curated Exhibition (7:5 staggered ledger, zero cards, gallery captions)',
  'Architectural Spine & Chapter Index (sticky left index rail + wide right stage)',
  'Full-Bleed Cinematic Stage + Interactive Split Scrubbing',
  'Print Monograph Spread (central book spine, drop-caps, oversized pull-quotes)',
  'Horizontal Filmstrip & Tabular Spec Matrix (zero vertical 3-card rows)',
  'Tactile Instrument Console (live interactive selectors replacing static lists)'
];

export const RHYTHM_PATTERNS = [
  'Wide-Narrow-Full (alternating 100vw full-bleed with 65% offset editorial column)',
  'Stepped Diagonal Cascade (progressive horizontal indentation across items)',
  'Monumental Anchor + Micro-Index (1 giant focal visual paired with compact tabular index)',
  'Dual-Plane Overlap (typography breaking across image boundaries with high-contrast backing)'
];

export const INTERACTION_MODELS = [
  'Live Scenario Switcher (tabs/filters replacing multi-card audience/pricing grids)',
  'Scroll-Linked Progress Spine + Image Scale Reveal (cubic-bezier(0.16, 1, 0.3, 1))',
  'Interactive Split-Screen Comparison / Stage Selector',
  'Accordion Ledger with Live Telemetry / Dynamic Quote Summary'
];

export function generateDesignSeed(seedInput = null) {
  const buf = seedInput
    ? crypto.createHash('sha384').update(String(seedInput)).digest()
    : crypto.randomBytes(48);
  const rawSeed = buf.toString('base64');
  const archetype = SPATIAL_ARCHETYPES[buf[0] % SPATIAL_ARCHETYPES.length];
  const rhythm = RHYTHM_PATTERNS[buf[1] % RHYTHM_PATTERNS.length];
  const interaction = INTERACTION_MODELS[buf[2] % INTERACTION_MODELS.length];
  const colSplitLeft = 52 + (buf[3] % 18); // 52..69%
  const colSplitRight = 100 - colSplitLeft;
  const minWhitespacePct = 35 + (buf[4] % 16); // 35..50%

  return {
    seed: seedInput || rawSeed,
    rawEntropy: rawSeed,
    spatialArchetype: archetype,
    sectionRhythm: [rhythm, RHYTHM_PATTERNS[(buf[5] || 1) % RHYTHM_PATTERNS.length]],
    interactionModel: interaction,
    derivedProportions: {
      colSplitLeft,
      colSplitRight,
      minWhitespacePct,
      summary: `${colSplitLeft}% / ${colSplitRight}% asymmetric split | min ${minWhitespacePct}% open canvas`
    },
    blindAssociationPrompt: `Derive 1 non-SaaS physical metaphor from "${rawSeed.slice(0, 12)}" before coding.`
  };
}

if (process.argv[1] && path.resolve(process.argv[1]) === __filename) {
  const args = process.argv.slice(2);
  const jsonFlag = args.includes('--json');
  const seedArg = args.find((a) => a.startsWith('--seed='))?.slice('--seed='.length);
  const positional = args.filter((a) => !a.startsWith('--'));

  if (seedArg || (positional[0] && isNaN(Number(positional[0])))) {
    const targetSeed = seedArg || positional[0];
    const result = generateDesignSeed(targetSeed);
    if (jsonFlag) {
      console.log(JSON.stringify(result, null, 2));
    } else {
      console.log(`SEED: ${result.seed} (${result.rawEntropy})`);
      console.log(`  ├─ Spatial Archetype   : ${result.spatialArchetype}`);
      console.log(`  ├─ Section Rhythm      : ${result.sectionRhythm[0]}`);
      console.log(`  ├─ Interaction Model   : ${result.interactionModel}`);
      console.log(`  ├─ Derived Proportions : ${result.derivedProportions.summary}`);
      console.log(`  └─ Blind Association   : ${result.blindAssociationPrompt}`);
    }
    process.exit(0);
  }

  const count = Math.max(1, Math.min(12, parseInt(positional[0] || '3', 10)));
  const items = [];
  for (let i = 1; i <= count; i++) {
    items.push(generateDesignSeed(null));
  }

  if (jsonFlag) {
    console.log(JSON.stringify(items, null, 2));
    process.exit(0);
  }

  console.log('================================================================================');
  console.log('WORLD-CLASS DESIGNER v2.1 — CRYPTOGRAPHIC ENTROPY SEEDS (48-BYTE BASE64)');
  console.log('MANDATORY: Complete the Blind Spatial Association BEFORE writing any HTML/CSS!');
  console.log('================================================================================\n');

  items.forEach((res, idx) => {
    console.log(`SEED_STREAM_${idx + 1}: ${res.rawEntropy}`);
    console.log(`  ├─ Spatial Archetype   : ${res.spatialArchetype}`);
    console.log(`  ├─ Section Rhythm      : ${res.sectionRhythm[0]}`);
    console.log(`  ├─ Interaction Model   : ${res.interactionModel}`);
    console.log(`  ├─ Derived Proportions : ${res.derivedProportions.summary}`);
    console.log(`  └─ Blind Association   : ${res.blindAssociationPrompt}\n`);
  });
}
