import React from 'react';
import {
  AbsoluteFill,
  interpolate,
  spring,
  useCurrentFrame,
  useVideoConfig,
} from 'remotion';

const CarSilhouette: React.FC<{ progress: number }> = ({ progress }) => (
  <svg
    viewBox="0 0 240 80"
    width="480"
    height="160"
    style={{
      opacity: progress,
      transform: `translateX(${interpolate(progress, [0, 1], [120, 0])}px)`,
      filter: `drop-shadow(0 0 32px rgba(255,107,53,0.7)) drop-shadow(0 0 60px rgba(255,179,71,0.4))`,
    }}
  >
    {/* Body */}
    <path
      d="M20 55 L28 35 Q34 25 55 24 L110 20 Q130 19 145 24 L170 26 Q188 28 200 38 L218 48 L220 55 Z"
      fill="url(#carGrad)"
    />
    {/* Roof */}
    <path
      d="M55 24 L68 14 Q78 8 100 7 L140 7 Q158 8 168 16 L180 24"
      fill="url(#carGrad)"
    />
    {/* Windows */}
    <path
      d="M70 23 L78 13 Q85 9 100 9 L130 9 Q142 10 148 16 L155 23 Z"
      fill="rgba(150,220,255,0.25)"
      stroke="rgba(150,220,255,0.15)"
      strokeWidth="0.5"
    />
    {/* Front wheel */}
    <circle cx="172" cy="57" r="13" fill="#1a1a1a" stroke="#FF6B35" strokeWidth="2" />
    <circle cx="172" cy="57" r="7" fill="#2a2a2a" stroke="#FFB347" strokeWidth="1" />
    <circle cx="172" cy="57" r="3" fill="#FFB347" />
    {/* Rear wheel */}
    <circle cx="62" cy="57" r="13" fill="#1a1a1a" stroke="#FF6B35" strokeWidth="2" />
    <circle cx="62" cy="57" r="7" fill="#2a2a2a" stroke="#FFB347" strokeWidth="1" />
    <circle cx="62" cy="57" r="3" fill="#FFB347" />
    {/* Ground shadow */}
    <ellipse cx="120" cy="72" rx="100" ry="6" fill="rgba(0,0,0,0.4)" />
    {/* Headlight glow */}
    <circle cx="215" cy="44" r="4" fill="#FFB347" opacity="0.9" />
    <circle cx="215" cy="44" r="10" fill="rgba(255,179,71,0.2)" />
    <defs>
      <linearGradient id="carGrad" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" stopColor="#2a2a3a" />
        <stop offset="50%" stopColor="#1a1a28" />
        <stop offset="100%" stopColor="#0f0f1a" />
      </linearGradient>
    </defs>
  </svg>
);

const Particles: React.FC<{ frame: number }> = ({ frame }) => {
  const particles = Array.from({ length: 18 }, (_, i) => {
    const seed = i * 137.508;
    const x = ((seed * 7.3) % 100);
    const y = ((seed * 3.7) % 100);
    const size = 1 + (i % 4);
    const speed = 0.3 + (i % 5) * 0.15;
    const offsetY = (frame * speed) % 100;
    const opacity = 0.15 + Math.sin((frame * 0.05) + i) * 0.12;
    const color = i % 3 === 0 ? '#FF6B35' : i % 3 === 1 ? '#FFB347' : '#ffffff';
    return { x, y: (y + offsetY) % 100, size, opacity, color };
  });

  return (
    <svg
      style={{ position: 'absolute', inset: 0, width: '100%', height: '100%', pointerEvents: 'none' }}
      viewBox="0 0 100 100"
      preserveAspectRatio="none"
    >
      {particles.map((p, i) => (
        <circle
          key={i}
          cx={p.x}
          cy={p.y}
          r={p.size * 0.15}
          fill={p.color}
          opacity={p.opacity}
        />
      ))}
    </svg>
  );
};

export const HeroAnimation: React.FC = () => {
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();

  const titleSpring = spring({ frame, fps, config: { damping: 18, stiffness: 80 }, delay: 10 });
  const subtitleSpring = spring({ frame, fps, config: { damping: 18, stiffness: 70 }, delay: 28 });
  const taglineSpring = spring({ frame, fps, config: { damping: 20, stiffness: 65 }, delay: 45 });
  const ctaSpring = spring({ frame, fps, config: { damping: 22, stiffness: 60 }, delay: 62 });
  const carSpring = spring({ frame, fps, config: { damping: 15, stiffness: 50 }, delay: 18 });
  const dividerSpring = spring({ frame, fps, config: { damping: 25, stiffness: 90 }, delay: 38 });

  const titleOpacity = interpolate(titleSpring, [0, 1], [0, 1]);
  const titleY = interpolate(titleSpring, [0, 1], [60, 0]);
  const subtitleOpacity = interpolate(subtitleSpring, [0, 1], [0, 1]);
  const subtitleY = interpolate(subtitleSpring, [0, 1], [40, 0]);
  const taglineOpacity = interpolate(taglineSpring, [0, 1], [0, 1]);
  const taglineY = interpolate(taglineSpring, [0, 1], [30, 0]);
  const ctaOpacity = interpolate(ctaSpring, [0, 1], [0, 1]);
  const ctaScale = interpolate(ctaSpring, [0, 1], [0.85, 1]);
  const dividerWidth = interpolate(dividerSpring, [0, 1], [0, 1]);

  const overlayOpacity = interpolate(frame, [0, 25], [1, 0], { extrapolateRight: 'clamp' });

  const pulseGlow = 0.6 + Math.sin(frame * 0.08) * 0.25;

  return (
    <AbsoluteFill style={{ backgroundColor: '#0A0A0A', fontFamily: 'system-ui, -apple-system, sans-serif', overflow: 'hidden' }}>

      {/* Gradient background */}
      <AbsoluteFill
        style={{
          background: 'radial-gradient(ellipse 120% 80% at 65% 55%, rgba(255,107,53,0.18) 0%, rgba(20,10,5,0) 65%), radial-gradient(ellipse 80% 60% at 20% 80%, rgba(255,179,71,0.10) 0%, transparent 55%), linear-gradient(160deg, #0A0A0A 0%, #12080a 35%, #1a0d0a 60%, #0f0805 100%)',
        }}
      />

      {/* Horizon glow line */}
      <AbsoluteFill
        style={{
          background: 'linear-gradient(to top, rgba(255,107,53,0.22) 0%, rgba(255,107,53,0.08) 8%, transparent 25%)',
        }}
      />

      {/* Grid lines subtle */}
      <AbsoluteFill
        style={{
          backgroundImage: `
            linear-gradient(rgba(255,107,53,0.04) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,107,53,0.04) 1px, transparent 1px)
          `,
          backgroundSize: '80px 80px',
          backgroundPosition: 'center bottom',
          maskImage: 'linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 60%)',
        }}
      />

      {/* Particles */}
      <Particles frame={frame} />

      {/* Car silhouette — right side */}
      <AbsoluteFill
        style={{
          display: 'flex',
          alignItems: 'flex-end',
          justifyContent: 'flex-end',
          paddingRight: 100,
          paddingBottom: 160,
        }}
      >
        <CarSilhouette progress={carSpring} />
      </AbsoluteFill>

      {/* Ambient glow behind car */}
      <AbsoluteFill
        style={{
          background: `radial-gradient(ellipse 35% 25% at 78% 72%, rgba(255,107,53,${pulseGlow * 0.18}) 0%, transparent 70%)`,
        }}
      />

      {/* Left content */}
      <AbsoluteFill
        style={{
          display: 'flex',
          flexDirection: 'column',
          justifyContent: 'center',
          paddingLeft: 120,
          paddingBottom: 60,
        }}
      >
        {/* Top badge */}
        <div
          style={{
            opacity: taglineOpacity,
            transform: `translateY(${taglineY}px)`,
            marginBottom: 28,
          }}
        >
          <span
            style={{
              display: 'inline-flex',
              alignItems: 'center',
              gap: 8,
              background: 'rgba(255,107,53,0.12)',
              border: '1px solid rgba(255,107,53,0.35)',
              borderRadius: 100,
              padding: '6px 20px',
              color: '#FFB347',
              fontSize: 15,
              fontWeight: 600,
              letterSpacing: '0.12em',
              textTransform: 'uppercase',
            }}
          >
            <span style={{ display: 'inline-block', width: 6, height: 6, borderRadius: '50%', background: '#FF6B35', boxShadow: '0 0 8px #FF6B35' }} />
            Honduras · Servicio Premium
          </span>
        </div>

        {/* Main title */}
        <div
          style={{
            opacity: titleOpacity,
            transform: `translateY(${titleY}px)`,
            marginBottom: 6,
          }}
        >
          <div
            style={{
              fontSize: 92,
              fontWeight: 900,
              lineHeight: 1.0,
              letterSpacing: '-0.02em',
              color: '#FFFFFF',
              textShadow: `0 0 80px rgba(255,107,53,0.4), 0 2px 20px rgba(0,0,0,0.8)`,
            }}
          >
            Sunset
          </div>
          <div
            style={{
              fontSize: 92,
              fontWeight: 900,
              lineHeight: 1.0,
              letterSpacing: '-0.02em',
              background: 'linear-gradient(135deg, #FF6B35 0%, #FFB347 50%, #FF8C42 100%)',
              WebkitBackgroundClip: 'text',
              WebkitTextFillColor: 'transparent',
              backgroundClip: 'text',
              textShadow: 'none',
              filter: `drop-shadow(0 0 30px rgba(255,107,53,${pulseGlow * 0.7}))`,
            }}
          >
            RenCar
          </div>
        </div>

        {/* Animated divider */}
        <div
          style={{
            width: `${interpolate(dividerWidth, [0, 1], [0, 320])}px`,
            height: 3,
            background: 'linear-gradient(90deg, #FF6B35, #FFB347, transparent)',
            borderRadius: 2,
            marginBottom: 28,
            boxShadow: '0 0 16px rgba(255,107,53,0.6)',
          }}
        />

        {/* Subtitle */}
        <div
          style={{
            opacity: subtitleOpacity,
            transform: `translateY(${subtitleY}px)`,
            marginBottom: 48,
          }}
        >
          <div
            style={{
              fontSize: 26,
              color: 'rgba(255,255,255,0.80)',
              fontWeight: 300,
              letterSpacing: '0.04em',
              lineHeight: 1.5,
              maxWidth: 560,
            }}
          >
            Alquiler de Vehículos en Honduras
          </div>
          <div
            style={{
              fontSize: 17,
              color: 'rgba(255,179,71,0.70)',
              fontWeight: 400,
              marginTop: 8,
              letterSpacing: '0.06em',
            }}
          >
            Flota moderna · Reserva en minutos · 24/7
          </div>
        </div>

        {/* CTA Buttons */}
        <div
          style={{
            opacity: ctaOpacity,
            transform: `scale(${ctaScale})`,
            display: 'flex',
            gap: 20,
            alignItems: 'center',
          }}
        >
          <div
            style={{
              padding: '18px 44px',
              background: 'linear-gradient(135deg, #FF6B35 0%, #FFB347 100%)',
              borderRadius: 50,
              color: '#0A0A0A',
              fontSize: 18,
              fontWeight: 700,
              letterSpacing: '0.03em',
              boxShadow: `0 8px 32px rgba(255,107,53,0.55), 0 0 60px rgba(255,107,53,0.2)`,
            }}
          >
            Reservar Ahora
          </div>
          <div
            style={{
              padding: '18px 36px',
              border: '1.5px solid rgba(255,107,53,0.5)',
              borderRadius: 50,
              color: 'rgba(255,255,255,0.85)',
              fontSize: 18,
              fontWeight: 500,
              letterSpacing: '0.03em',
            }}
          >
            Ver Flota →
          </div>
        </div>
      </AbsoluteFill>

      {/* Vignette */}
      <AbsoluteFill
        style={{
          background: 'radial-gradient(ellipse 100% 100% at 50% 50%, transparent 50%, rgba(0,0,0,0.55) 100%)',
          pointerEvents: 'none',
        }}
      />

      {/* Black flash intro */}
      {overlayOpacity > 0 && (
        <AbsoluteFill style={{ backgroundColor: `rgba(0,0,0,${overlayOpacity})` }} />
      )}

      {/* Bottom info bar */}
      <AbsoluteFill
        style={{
          display: 'flex',
          alignItems: 'flex-end',
          justifyContent: 'space-between',
          padding: '0 120px 48px',
          opacity: ctaOpacity,
        }}
      >
        <div style={{ color: 'rgba(255,255,255,0.3)', fontSize: 13, letterSpacing: '0.15em', textTransform: 'uppercase' }}>
          sunsetrentcar.com
        </div>
        <div style={{ display: 'flex', gap: 32 }}>
          {['Tegucigalpa', 'San Pedro Sula', 'La Ceiba'].map((city) => (
            <div key={city} style={{ color: 'rgba(255,179,71,0.5)', fontSize: 12, letterSpacing: '0.1em' }}>
              {city}
            </div>
          ))}
        </div>
      </AbsoluteFill>
    </AbsoluteFill>
  );
};
