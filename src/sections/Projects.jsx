import { ArrowUpRight, Github } from "lucide-react";
import {AnimatedBorderButton} from "@/components/AnimatedBorderButton"

const projects = [
    {
        title: "Mijn Portfolio",
        description: "Mijn eerste grote stap in React. Ik heb dit project gebruikt om de basis van component-architectuur en hooks onder de knie te krijgen, waarbij ik een tutorial als blauwdruk heb gebruikt en deze heb aangepast naar mijn eigen stijl.",
        image: "/react1.png",
        tags: ["React", "Tailwind CSS", "Lucide React", "Framer Motion", "Git"],
        link: "https://jouw-demo-link.vercel.app",
        github: "https://github.com/jouw-gebruikersnaam/jouw-repo"
    },
    {
        title: "GameStars Full-Stack Platform",
        description: "Een interactief gaming platform waar ik PHP en HTML5 heb gecombineerd tot een dynamisch geheel. In plaats van statische pagina's, heb ik een systeem ontwikkeld waarbij PHP-logica de HTML-structuur aanstuurt. Dit zorgt ervoor dat game-content, reviews en media automatisch op de juiste plek verschijnen vanuit een centrale data-structuur.",
        image: "/Gamestars2.png",
        tags: ["PHP", "HTML5", "Vanilla CSS", "JavaScript", "Dynamic Rendering"],
        link: "https://jouw-link-hier.nl",
        github: "https://github.com/jouw-gebruikersnaam/gamestars-php"
    }
];

export const Projects = () => {
    return (
        <section id="about" className="py-32 relative overflow-hidden">
            {/*bg */}
            <div className="absolute top-1/4 right-0 w-96 h-96 bg-primary/5 rounded-full blur-3xl" />
            <div className="absolute bottom-1/4 left-0 w-64 h-64 bg-highlight/5 rounded-full blur-3xl" />
            
            <div className="container mx-auto px-6 relative z-10">
                {/*Header */}
                <div className="text-center mx-auto max-w-3xl mb-16">
                    <span className="text-secondary-foreground text-sm font-medium tracking-wider uppercase animate-fade-in">
                        Mijn werk
                    </span>
                    <h2 className="text-4xl md:text-5xl font-bold mt-4 mb-6 animate-fade-in text-secondary-foreground">
                        Projecten met focus op <span className="font-serif italic font-normal text-white">groei en resultaat</span>
                    </h2>
                </div>

                {/*Grid */}
                <div className="grid md:grid-cols-2 gap-8">
                    {projects.map((project, idx) => (
                        <div 
                            key={idx} 
                            className="group glass rounded-2xl overflow-hidden animate-fade-in md:row-span-1 border border-white/10" 
                            style={{ animationDelay: `${(idx + 1) * 100}ms` }}
                        >
                            <div className="relative overflow-hidden aspect-video">
                                <img 
                                    src={project.image} 
                                    alt={project.title} 
                                    className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                />
                                
                                {/* Overlay Gradient */}
                                <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />

                                {/* Links op hover */}
                                <div className="absolute inset-0 flex items-center justify-center gap-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <a href={project.link} className="p-3 rounded-full glass hover:bg-primary text-white transition-all">
                                        <ArrowUpRight className="w-5 h-5" />
                                    </a>
                                    <a href={project.github} className="p-3 rounded-full glass hover:bg-primary text-white transition-all">
                                        <Github className="w-5 h-5" />
                                    </a>
                                </div>
                            </div>

                            {/*Content */}
                            <div className="p-6 space-y-4">
                                <div className="flex items-start justify-between">
                                    <h3 className="text-xl font-semibold group-hover:text-primary transition-colors">
                                        {project.title}
                                    </h3>
                                    <ArrowUpRight className="w-5 h-5 text-muted-foreground group-hover:text-primary group-hover:translate-x-1 group-hover:-translate-y-1 transition-all" />
                                </div>
                                <p className="text-muted-foreground text-sm leading-relaxed">
                                    {project.description}
                                </p>
                                <div className="flex flex-wrap gap-2 pt-2">
                                    {project.tags.map((tag, tagIdx) => (
                                        <span key={tagIdx} className="text-[10px] uppercase tracking-wider px-2 py-1 bg-white/5 border border-white/10 rounded-md text-primary">
                                            {tag}
                                        </span>
                                    ))}
                                </div>
                            </div>
                            
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
};