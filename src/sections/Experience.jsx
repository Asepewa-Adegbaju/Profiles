import React from 'react';

const experiences = [
  {
    period: "Sept 2025 — Heden",
    role: "Software Development Student",
    company: "Opleiding & Praktijk",
    description: "Verdieping in software architectuur.",
    technologies: ["PHP", "JavaScript", "HTML5"],
    current: true,
  },
  {
    period: "Zomer 2025 — Sept 2025",
    role: "Autodidact Web Developer",
    company: "Zelfstudie & GameStars Project",
    description: "Start traject van zelfstudie.",
    technologies: ["PHP", "JavaScript"],
    current: false,
  }
];

export const Experience = () => {
  return (
    <section id="experience" className="py-32 relative overflow-hidden bg-glow">
      {/* Background Decor */}
      <div className="absolute top-1/2 left-1/4 w-96 h-96 bg-primary/5 rounded-full blur-3xl -translate-y-1/2" />
      
      <div className="container mx-auto px-6 relative z-10">
        {/* Header Section */}
        <div className="max-w-3xl mb-16">
          <span className="text-secondary-foreground text-sm font-medium tracking-wider uppercase">
            Mijn Ontwikkeling
          </span>
          <h2 className="text-4xl md:text-5xl font-bold mt-4 mb-6 text-secondary-foreground">
            Een bewezen <span className="font-serif italic font-normal text-white">leercurve.</span>
          </h2>
          <p className="text-muted-foreground max-w-2xl">
            Een tijdlijn van mijn groei als developer: gestart als nieuwsgierige autodidact in de zomer van 2025.
          </p>
        </div>

        {/* Line */}
        <div className="relative">
          <div className="timeline-glow absolute left-0 md:left-1/2 top-0 bottom-0 w-[2px] bg-primary/30 md:-translate-x-1/2" />
          
          {/* Container */}
          <div className="space-y-12">
            {experiences.map((experience, idx) => (
              <div key={idx} className="relative grid md:grid-cols-2 gap-8 animate-fade-in" style={{ animationDelay: `${(idx + 1) * 150}ms` }}>
                {/* Dot */}
                <div className="absolute left-0 md:left-1/2 w-3 h-3 bg-primary rounded-full -translate-x-1/2 mt-2"></div>
                
                {/* De Content Box */}
                <div className={`pl-8 md:pl-0 ${idx % 2 === 0 ? "md:pr-16 md:text-right" : "md:col-start-2 md:pl-16"}`}>
                   <div className={`glass p-6 rounded-2xl border border-primary/30 hover:border-primary/50 transition-all duration-500`}>
                      <span className="text-primary font-medium">{experience.period}</span>
                      <h3 className="text-xl font-semibold mt-2">{experience.role}</h3>
                      <p className="text-muted-foreground">{experience.company}</p>
                      <p className="text-sm text-muted-foreground mt-4">{experience.description}</p>
                      <div className={`flex flex-wrap gap-2 mt-4 ${idx % 2 === 0 ? "md:justify-end" : ""}`}>
                        {experience.technologies.map((tech, techIdx) => (
                            <span key={techIdx} className="px-3 py-1 bg-surface text-xs rounded-full text-muted-foreground">
                              {tech}
                            </span>
                        ))}
                      </div>
                   </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
};