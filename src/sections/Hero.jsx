import { Button } from "@/components/Button"
import { ArrowRight, Download, Github, Linkedin,ChevronDown } from "lucide-react"
import { AnimatedBorderButton } from "@/components/AnimatedBorderButton"

const skills = [
    "JavaScript (ES6+)",
    "HTML",
    "CSS",
    "Git",
    "GitHub",
    "React",
    "Tailwind Css",
    "PHP",
    "VS Code"
]

export const Hero = () => {
    return (
        <section className="relative min-h-screen flex items-center overflow-hidden">
            {/*background*/}
            <div className="absolute inset-0"> 
                <img className="w-full h-full object-cover opacity-40" src="/bg-red.png" alt="Hero background" /> 
            </div>
            <div className="absolute inset-0 bg-gradient-to-b from-background/20 via-background/80 to-background" />
            
            {/*Dots*/}
            <div className="absolute inset-0 overflow-hidden pointer-events-none">
                {[...Array(30)].map((_, i) => (
                    <div key={i} className="absolute w-1.5 h-1.5 rounded-full opacity-60"
                        style={{
                            backgroundColor: "#e63946",
                            left: `${Math.random() * 100}%`,
                            top: `${Math.random() * 100}%`,
                            animation: `slow-drift ${Math.random() * 20}s ease-in-out infinite`,
                            animationDelay: `-${Math.random() * 5}s`,
                        }}
                    />
                ))}
            </div>

            {/*Content*/}
            <div className="container mx-auto px-6 pt-32 pb-20 relative z-10">
                <div className="grid lg:grid-cols-2 gap-12 items-center">
                    {/*left content (text)*/}
                    <div className="space-y-8">
                        <div className="animate-fade-in">
                            <span className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-glass text-sm text-primary">
                                <span className="w-2 h-2 bg-primary rounded-full animate-pulse" />
                                Software Developer · Emerging Fullstack Explorer
                            </span>
                        </div>
                        
                        {/*headline*/}
                        <div className="space-y-4">
                            <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight animate-fade-in animation-delay-200">
                                Mastering the <span className="text-primary glow-text">core</span><br />
                                to create limitless <br />
                                <span className="font-serif italic font-normal text-white">digital solutions</span>.
                            </h1>
                            <p className="text-lg text-muted-foreground max-w-lg animate-fade-in animation-delay-400">
                                Hoi, ik ben Asepewa Adegbaju. Een gedreven Software Developer met een passie voor het complete plaatje.
                                Ik bouw aan mijn skills in de volledige stack om complexe ideeën om te zetten in functionele code.
                            </p>
                        </div>

                        {/*cta*/}
                        <div className="flex flex-wrap gap-4 animate-fade-in animation-delay-300">
                            <Button size="lg" href="mailto:asepewa123@gmail.com">Contact <ArrowRight className="w-5 h-5" /></Button>
                            <AnimatedBorderButton>
                        <a 
                        href="/asepewa_adegbaju.pdf" 
                         download="CV_Asepewa_Adegbaju.pdf" 
                         className="flex items-center gap-2 w-full h-full"
                         >
                        <Download className="w-5 h-5" /> Download CV
                         </a>
                        </AnimatedBorderButton>
                        </div>

                        {/*socials*/}
                        <div className="flex items-center gap-4 animate-fade-in delay-500">
                            <span className="text-sm text-muted-foreground">Volg mij</span>
                            {[
                                { icon: Github, href: "https://github.com/asepewa-adegbaju" },
                                { icon: Linkedin, href: "https://www.linkedin.com/in/asepewa-adegbaju-a8134b3ab" }
                            ].map((social, i) => (
                                <a key={i} href={social.href} target="_blank" rel="noopener noreferrer" className="p-2 rounded-full glass hover:bg-primary/10 hover:text-primary transition-all duration-300">
                                    <social.icon className="w-5 h-5" />
                                </a>
                            ))}
                        </div>
                    </div>

                    {/*right content (pfp)*/}
                    <div className="relative animate-fade-in animation-delay-300">
                        <div className="relative max-w-md mx-auto">
                            <div className="absolute inset-0 rounded-3xl bg-gradient-to-br from-primary/30 via-transparent to-primary/10 blur-2xl animate-pulse" />
                            <div className="relative rounded-3xl p-2 glow-border">
                                <img src="/Asepewa.jpeg" alt="Asepewa Adegbaju" className="aspect-[4/5] object-cover rounded-2xl" />
                            </div>

                            {/*floating card*/}
                            <div className="absolute -bottom-4 -right-4 bg-glass rounded px-4 py-3 animate-float">
                                <div className="flex items-center gap-3">
                                    <div className="w-3 h-3 bg-green-500 rounded-full animate-pulse shadow-[0_0_8px_#22c55e]" />
                                    <span className="text-sm font-medium text-primary">Beschikbaar voor stage</span>
                                </div>
                            </div>

                            {/*stats*/}
                            <div className="absolute -top-4 -left-4 glass rounded-2xl px-4 py-3 animate-float animation-delay-500">
                                <div className="text-2xl font-bold text-primary">7+ maanden</div>
                                <div className="text-xs text-white uppercase tracking-tighter">Ervaring</div>
                                <div className="mt-2 flex flex-wrap gap-1 max-w-[150px]">
                                    {skills.slice(0, 3).map((skill, idx) => (
                                        <span key={idx} className="text-[10px] bg-primary/10 px-1 rounded text-primary border border-primary/20">{skill}</span>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

               {/* Skills Section */}
        <div className="mt-20 animate-fade-in animation-delay-600">
          <p className="text-sm text-muted-foreground mb-6 text-center">
            Technologies I work with
          </p>
          <div className="relative overflow-hidden">
            <div
              className="absolute left-0 top-0 bottom-0 w-32
             bg-gradient-to-r from-background to-transparent z-10"
            />
            <div
              className="absolute right-0 top-0 bottom-0 w-32
             bg-gradient-to-l from-background to-transparent z-10"
            />
            <div className="flex animate-marquee">
              {[...skills, ...skills].map((skill, idx) => (
                <div key={idx} className="flex-shrink-0 px-8 py-4">
                  <span className="text-xl font-semibold text-muted-foreground/50 hover:text-muted-foreground transition-colors">
                    {skill}
                  </span>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>

      <div
        className="absolute bottom-8 left-1/2 -translate-x-1/2 
      animate-fade-in animation-delay-800"
      >
        <a
          href="#about"
          className="flex flex-col items-center gap-2 text-muted-foreground hover:text-primary transition-colors group"
        >
          <span className="text-xs uppercase tracking-wider">Scroll</span>
          <ChevronDown className="w-6 h-6 animate-bounce" />
        </a>
      </div>
        </section>
    )
}